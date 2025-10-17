import os
import json
from flask import Flask, request, jsonify
from flask_cors import CORS

try:
    import ollama
except Exception as e:
    ollama = None

app = Flask(__name__)
CORS(app)

MODEL_NAME = os.getenv('GEMMA_MODEL', 'gemma:2b-instruct')
OLLAMA_HOST = os.getenv('OLLAMA_HOST', 'http://localhost:11434')


@app.route('/health', methods=['GET'])
def health():
    if ollama is None:
        return jsonify({
            'ok': False,
            'error': 'Python package "ollama" not installed. pip install ollama',
        }), 500
    try:
        
        _ = ollama.Client(host=OLLAMA_HOST).list()
        return jsonify({'ok': True, 'model': MODEL_NAME})
    except Exception as e:
        return jsonify({'ok': False, 'error': str(e), 'model': MODEL_NAME}), 500


@app.route('/api/suggest', methods=['POST'])
def suggest():
    if ollama is None:
        return jsonify({'error': 'ollama Python package not installed (pip install ollama)'}), 500

    data = request.json or {}
    name = data.get('name', 'Student')
    level = data.get('level', 'intermediate')
    mood = data.get('mood', 'neutral')
    duration = int(data.get('duration', 60))  
    strengths = data.get('strengths') or []  
    weaknesses = data.get('weaknesses') or []  

    system_prompt = (
        "You are an expert study coach. You generate short, practical tasks students can do "
        "in the available time. Prefer tasks that leverage strengths to build momentum and target "
        "weaknesses with focused practice. If no strengths/weaknesses are provided, give general guidance. "
        "Output strictly JSON: {\"suggestions\":[\"...\"]}. No extra text."
    )

    strengths_text = ", ".join(str(x) for x in strengths) if strengths else "(none)"
    weaknesses_text = ", ".join(str(x) for x in weaknesses) if weaknesses else "(none)"
    user_prompt = (
        f"Student: {name}\n"
        f"Level: {level}\n"
        f"Mood: {mood}\n"
        f"Strengths: {strengths_text}\n"
        f"Weaknesses: {weaknesses_text}\n"
        f"Time available: {duration} minutes\n"
        "Return a JSON object with a key 'suggestions' that is an array of 3-5 bullet-point style strings. "
        "Each item should be a single, concise, actionable activity that fits within the time."
    )

    try:
        client = ollama.Client(host=OLLAMA_HOST)
        resp = client.chat(
            model=MODEL_NAME,
            messages=[
                {"role": "system", "content": system_prompt},
                {"role": "user", "content": user_prompt},
            ],
            options={
                "temperature": 0.6,
                "num_ctx": 2048,
            },
        )
        content = resp.get('message', {}).get('content', '').strip()
        suggestions: list[str] = []
        if content:
            try:
                parsed = json.loads(content)
                if isinstance(parsed, dict) and isinstance(parsed.get('suggestions'), list):
                    suggestions = [str(x).strip() for x in parsed.get('suggestions') if str(x).strip()]
            except Exception:
                lines = [ln.strip(' \t•-') for ln in content.splitlines()]
                suggestions = [ln for ln in lines if ln]
        if not suggestions:
            suggestions = [
                "Write 5 flashcards on a tricky topic and quiz yourself twice.",
                "Solve 3 practice problems and check your answers.",
                "Summarize one lecture in 5 bullet points and mark unclear areas to ask later.",
            ]
        return jsonify({'duration': duration, 'suggestions': suggestions})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


if __name__ == '__main__':
    app.run(port=5050, debug=True)

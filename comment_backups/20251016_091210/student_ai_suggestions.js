// Student AI Suggestions Integration
// This script adds AI-powered suggestions to the Free Hours section

// --- CONFIG ---
const AI_API_URL = 'http://localhost:5050/api/suggest'; // Flask backend endpoint

function getStudentInfo() {
    // Extract student info from DOM (customize as needed)
    const name = document.getElementById('studentName')?.textContent?.trim() || 'Student';
    // For demo, use hardcoded level and mood, or prompt user for input
    const level = 5; // Example: from Personal Growth section
    const mood = 'neutral'; // Could be set via UI
    const duration = Number(document.getElementById('aiDuration')?.value || 60);
    const strengths = (document.getElementById('aiStrengths')?.value || '')
        .split(',')
        .map(s => s.trim())
        .filter(Boolean);
    const weaknesses = (document.getElementById('aiWeaknesses')?.value || '')
        .split(',')
        .map(s => s.trim())
        .filter(Boolean);
    return { name, level, mood, duration, strengths, weaknesses };
}

function showAISuggestion(content, loading = false, error = false) {
    const container = document.getElementById('aiSuggestionBox');
    if (!container) return;
    if (loading) {
        container.innerHTML = `<div style="color: #7c3aed; font-weight: 600;">Generating suggestion...</div>`;
    } else if (error) {
        container.innerHTML = `<div style="color: #fb7185; font-weight: 600;">${content}</div>`;
    } else {
        if (Array.isArray(content)) {
            const list = content.map(item => `<li style="margin-left:18px;">${item}</li>`).join('');
            container.innerHTML = `<ul style="margin-top:6px;display:flex;flex-direction:column;gap:6px;">${list}</ul>`;
        } else {
            container.innerHTML = `<div style="color: #059669; font-weight: 600;">${content}</div>`;
        }
    }
}

function fetchAISuggestion() {
    const student = getStudentInfo();
    showAISuggestion('', true);
    fetch(AI_API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(student)
    })
    .then(res => res.json())
    .then(data => {
        if (data && Array.isArray(data.suggestions)) {
            showAISuggestion(data.suggestions);
        } else if (data && data.suggestion) {
            // Backward compatibility if server returns single string
            showAISuggestion([data.suggestion]);
        } else {
            showAISuggestion('No suggestion received.', false, true);
        }
    })
    .catch(err => {
        showAISuggestion('Error fetching suggestion. Please try again.', false, true);
    });
}

// --- UI Integration ---
document.addEventListener('DOMContentLoaded', function() {
    // Find Free Hours section and inject AI suggestion box/button
    const idealHourSection = document.getElementById('page-ideal-hour');
    if (!idealHourSection) return;
    // Insert AI suggestion UI after the title
    const title = idealHourSection.querySelector('h2');
    if (title) {
        const aiBox = document.createElement('div');
        aiBox.id = 'aiSuggestionBox';
        aiBox.style = 'margin: 16px 0; padding: 16px; background: #f9fafb; border-radius: 8px; border: 1px solid #eef2f7;';
        aiBox.innerHTML = '<em>Get personalized activity suggestions for your free hour!</em>';
        title.parentNode.insertBefore(aiBox, title.nextSibling);

        const aiBtn = document.createElement('button');
        aiBtn.textContent = 'Get AI Suggestion';
        aiBtn.className = 'btn primary';
        aiBtn.style = 'margin-top: 8px;';
        aiBtn.onclick = fetchAISuggestion;
        aiBox.appendChild(aiBtn);

        // Duration selector (optional)
        const durWrap = document.createElement('div');
        durWrap.style = 'margin-top: 8px; display:flex; gap:8px; align-items:center;';
        durWrap.innerHTML = '<label for="aiDuration" style="font-size:13px;color:#555;">Time available:</label>' +
                            '<select id="aiDuration" style="padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;">' +
                            '<option value="30">30 min</option>' +
                            '<option value="45">45 min</option>' +
                            '<option value="60" selected>60 min</option>' +
                            '<option value="90">90 min</option>' +
                            '</select>';
        aiBox.appendChild(durWrap);

        // Strengths / Weaknesses inputs (optional)
        const swWrap = document.createElement('div');
        swWrap.style = 'margin-top: 8px; display:grid; grid-template-columns: 1fr 1fr; gap:8px;';
        swWrap.innerHTML = '<input id="aiStrengths" type="text" placeholder="Strengths (comma-separated)" ' +
                           'style="padding:8px;border:1px solid #e5e7eb;border-radius:6px;"/>' +
                           '<input id="aiWeaknesses" type="text" placeholder="Weaknesses (comma-separated)" ' +
                           'style="padding:8px;border:1px solid #e5e7eb;border-radius:6px;"/>';
        aiBox.appendChild(swWrap);
    }
});

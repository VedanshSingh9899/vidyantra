async function onClickLogin() {
    try {
        const form = document.querySelector('.form-group.active');
        if (!form) return;

        
        const formId = form.id; 
        const userInput = form.querySelector('input[type="text"], input[type="email"]');
        const passInput = form.querySelector('input[type="password"]');
        const username = (userInput && userInput.value || '').trim();
        const password = (passInput && passInput.value || '').trim();

        if (!username || !password) {
            alert('Please enter username and password.');
            return;
        }

        const btn = form.querySelector('.login-btn');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Signing in...';
        }

        
        const res = await fetch('login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        if (!res) {
            throw new Error('Network response was not ok');
        }

        const data = await res.json().catch(() => ({}));

        if (!data || data.success !== true) {
            const msg = (data && data.error) ? data.error : 'Login failed.';
            alert(msg);
            if (btn) {
                btn.disabled = false;
                btn.textContent = (formId === 'institute-signin') ? 'Sign In' : 'Login';
            }
            return;
        }


        
        const role = (data.role || '').toLowerCase();
        if (role === 'student') {
            window.location.href = 'https://student-site-mpgh.onrender.com/';
        } else if (role === 'teacher') {
            window.location.href = 'https://teacher-site.onrender.com/';
        } else if (role === 'institute' || role === 'admin') {
            window.location.href = 'https://admin-site-08sb.onrender.com/';
            
        } else {
            window.location.href = 'https://student-site-mpgh.onrender.com/';
        }
        return;
    } catch (err) {
        alert('Unexpected error. Please try again.');
        console.error(err);
    }
}

function switchRole(role) {
    const slider = document.querySelector('.slider');
    const buttons = document.querySelectorAll('.toggle-button');
    const forms = document.querySelectorAll('.form-group');
    const loginBox = document.querySelector('.login_box');

    buttons.forEach(btn => btn.classList.remove('active'));
    forms.forEach(form => form.classList.remove('active'));

    if (role === 'student') {
        slider.className = 'slider student-active';
        buttons[0].classList.add('active');
        document.getElementById('student').classList.add('active');
        loginBox.classList.remove('slide-center', 'slide-right');
        loginBox.classList.add('slide-left');
    }
    else if (role === 'teacher') {
        slider.className = 'slider teacher-active';
        buttons[1].classList.add('active');
        document.getElementById('teacher').classList.add('active');
        loginBox.classList.remove('slide-left', 'slide-right');
        loginBox.classList.add('slide-center');
    }
    else if (role === 'institute') {
        slider.className = 'slider institute-active';
        buttons[2].classList.add('active');
        showInstituteSignin();
        loginBox.classList.remove('slide-left', 'slide-center');
        loginBox.classList.add('slide-right');
    }
}

function showInstituteSignin() {
    document.getElementById('institute-signin').classList.add('active');
    document.getElementById('institute-step1').classList.remove('active');
    document.getElementById('institute-step2').classList.remove('active');
    document.getElementById('institute-step3').classList.remove('active');
}

function showInstituteSignup() {
    document.getElementById('institute-signin').classList.remove('active');
    document.getElementById('institute-step1').classList.add('active');
    document.getElementById('institute-step2').classList.remove('active');
    document.getElementById('institute-step3').classList.remove('active');
}

function goToStep(step) {
    const steps = [1, 2, 3];
    steps.forEach(i => {
        document.getElementById(`institute-step${i}`).classList.remove('active');
    });
    document.getElementById(`institute-step${step}`).classList.add('active');
}

function togglePassword(btn) {
    const input = btn.previousElementSibling;
    const icon = btn.querySelector('i');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function dummyVerify() {
    alert("✅ Email verified successfully (dummy front-end).");
    switchRole('student'); 
}
function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function filled(){
    const continueBtn = document.getElementById('signBtn1');
    const inputs = document.querySelectorAll('#institute-step1 input');
    let allFilled = true;
    inputs.forEach(input => {
        if (input.value.trim() === '') {
            allFilled = false;
        }  
    });
    if (allFilled) {
        continueBtn.disabled = false;
        continueBtn.classList.add('enabled');
    } else {
        continueBtn.disabled = true;
        continueBtn.classList.remove('enabled');
    }


}

document.addEventListener('DOMContentLoaded', () => {
    const inputs = Array.from(document.querySelectorAll('#institute-step1 input'));
    const continueBtn = document.getElementById('signBtn1');
    if (!continueBtn) return;

    
    filled();

    inputs.forEach(i => i.addEventListener('input', filled));

    
    continueBtn.addEventListener('click', (e) => {
        
        filled();
        if (continueBtn.disabled) {
            e.preventDefault();
            alert('Please fill all required fields');
            return;
        }
        
        goToStep(2);
    });

    
    const emailInput = document.getElementById('instEmail');
    const phoneInput = document.getElementById('instPhone');
    const pwdInput = document.getElementById('instPassword');
    const pwdConfirm = document.getElementById('instPasswordConfirm');
    const signBtn2 = document.getElementById('signBtn2');
    
    
    const pwdFill = document.getElementById('pwdFill');
    const pwdText = document.getElementById('pwdText');

    if (!signBtn2) return;

    function scorePassword(pwd) {
        let score = 0;
        if (!pwd) return 0;
        if (pwd.length >= 8) score += 1;
        if (/[A-Z]/.test(pwd)) score += 1;
        if (/[0-9]/.test(pwd)) score += 1;
        if (/[^A-Za-z0-9]/.test(pwd)) score += 1;
        return score; 
    }

    function updatePwdUI() {
        const val = pwdInput.value || '';
        const score = scorePassword(val);
        const pct = Math.min(100, (score / 4) * 100);
        
        const strengthEl = pwdFill && pwdFill.parentElement && pwdFill.parentElement.parentElement ? pwdFill.parentElement.parentElement : null;
        if (strengthEl) {
            strengthEl.style.display = val ? 'flex' : 'none';
        }
        if (pwdFill) pwdFill.style.width = pct + '%';
        if (score <= 1) {
            pwdText.textContent = 'Too weak';
        } else if (score === 2) {
            pwdText.textContent = 'Weak';
        } else if (score === 3) {
            pwdText.textContent = 'Good';
        } else {
            pwdText.textContent = 'Strong';
        }
    }

    function validateStep2() {
        let ok = true;
        
        const emailVal = (emailInput && emailInput.value || '').trim();
        if (!emailInput || !validateEmail(emailVal)) {
            if (emailInput) emailInput.classList.add('input-invalid');
            ok = false;
        } else {
            if (emailInput) emailInput.classList.remove('input-invalid');
        }

        
        const phoneDigits = (phoneInput && phoneInput.value || '').replace(/\D/g, '');
        if (!phoneInput || !phoneInput.value.trim() || phoneDigits.length !== 10) {
            if (phoneInput) phoneInput.classList.add('input-invalid');
            ok = false;
        } else {
            if (phoneInput) phoneInput.classList.remove('input-invalid');
        }

        
        const pwd = (pwdInput && pwdInput.value) || '';
        const conf = (pwdConfirm && pwdConfirm.value) || '';
        const score = scorePassword(pwd);
        if (!pwd || score < 3) {
            if (pwdInput) pwdInput.classList.add('input-invalid');
            ok = false;
        } else {
            if (pwdInput) pwdInput.classList.remove('input-invalid');
        }
        if (pwd && conf && pwd !== conf) {
            if (pwdConfirm) pwdConfirm.classList.add('input-invalid');
            ok = false;
        } else {
            if (pwdConfirm) pwdConfirm.classList.remove('input-invalid');
        }

        
        if (signBtn2) signBtn2.classList.toggle('enabled', ok);
        return ok;
    }

    
    [emailInput, phoneInput, pwdInput, pwdConfirm].forEach(el => {
        if (!el) return;
        el.addEventListener('input', () => {
            updatePwdUI();
            validateStep2();
        });
    });

    
    if (signBtn2) {
        
        signBtn2.disabled = false;
        signBtn2.addEventListener('click', (e) => {
            const ok = validateStep2();
            if (!ok) {
                
                const firstInvalid = document.querySelector('.input-invalid');
                if (firstInvalid) firstInvalid.focus();
                return;
            }
            
            goToStep(3);
        });
    }
})
// login_ajax.js
// Handles AJAX login and redirects

function ajaxLogin(role) {
    let username, password;
    if (role === 'student') {
        username = document.querySelector('#student input[type="text"]').value.trim();
        password = document.querySelector('#student input[type="password"]').value.trim();
    } else if (role === 'teacher') {
        username = document.querySelector('#teacher input[type="text"]').value.trim();
        password = document.querySelector('#teacher input[type="password"]').value.trim();
    } else if (role === 'institute') {
        username = document.querySelector('#institute-signin input[type="email"]').value.trim();
        password = document.querySelector('#institute-signin input[type="password"]').value.trim();
    }
    if (!username || !password) {
        showLoginError(role, 'Please enter both username and password.');
        return;
    }
    fetch('login_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (data.userType === 'student') {
                window.location.href = '../student_site.html';
            } else if (data.userType === 'teacher') {
                window.location.href = '../teacher_site.html';
            } else if (data.userType === 'institute') {
                window.location.href = '../admin_site.html';
            }
        } else {
            showLoginError(role, data.error || 'Login failed.');
        }
    })
    .catch(() => {
        showLoginError(role, 'Server error. Please try again.');
    });
}

function showLoginError(role, msg) {
    let form;
    if (role === 'student') form = document.getElementById('student');
    else if (role === 'teacher') form = document.getElementById('teacher');
    else if (role === 'institute') form = document.getElementById('institute-signin');
    let err = form.querySelector('.error-message');
    if (!err) {
        err = document.createElement('div');
        err.className = 'error-message';
        form.insertBefore(err, form.firstChild.nextSibling);
    }
    err.textContent = msg;
}

// Attach listeners
window.addEventListener('DOMContentLoaded', function() {
    document.querySelector('#student .login-btn').onclick = function() { ajaxLogin('student'); };
    document.querySelector('#teacher .login-btn').onclick = function() { ajaxLogin('teacher'); };
    document.querySelector('#institute-signin .login-btn').onclick = function() { ajaxLogin('institute'); };
});

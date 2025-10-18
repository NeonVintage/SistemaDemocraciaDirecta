// Authentication JavaScript

function showTab(tab) {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const tabButtons = document.querySelectorAll('.tab-button');

    if (tab === 'login') {
        loginForm.classList.add('active');
        registerForm.classList.remove('active');
        tabButtons[0].classList.add('active');
        tabButtons[1].classList.remove('active');
    } else {
        loginForm.classList.remove('active');
        registerForm.classList.add('active');
        tabButtons[0].classList.remove('active');
        tabButtons[1].classList.add('active');
    }
}

function showMessage(elementId, message, type) {
    const messageEl = document.getElementById(elementId);
    messageEl.textContent = message;
    messageEl.className = `message show ${type}`;
    setTimeout(() => {
        messageEl.classList.remove('show');
    }, 5000);
}

async function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'login',
                email: email,
                password: password
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage('login-message', 'Inicio de sesión exitoso. Redirigiendo...', 'success');
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 1000);
        } else {
            showMessage('login-message', data.message || 'Error al iniciar sesión', 'error');
        }
    } catch (error) {
        showMessage('login-message', 'Error de conexión. Intente nuevamente.', 'error');
        console.error('Error:', error);
    }
    
    return false;
}

async function handleRegister(event) {
    event.preventDefault();
    
    const nombre = document.getElementById('reg-nombre').value;
    const apellido = document.getElementById('reg-apellido').value;
    const email = document.getElementById('reg-email').value;
    const password = document.getElementById('reg-password').value;
    const passwordConfirm = document.getElementById('reg-password-confirm').value;
    
    if (password !== passwordConfirm) {
        showMessage('register-message', 'Las contraseñas no coinciden', 'error');
        return false;
    }
    
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'register',
                nombre: nombre,
                apellido: apellido,
                email: email,
                password: password
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage('register-message', 'Registro exitoso. Por favor revisa tu correo para confirmar tu cuenta.', 'success');
            document.querySelector('#register-form form').reset();
        } else {
            showMessage('register-message', data.message || 'Error al registrarse', 'error');
        }
    } catch (error) {
        showMessage('register-message', 'Error de conexión. Intente nuevamente.', 'error');
        console.error('Error:', error);
    }
    
    return false;
}


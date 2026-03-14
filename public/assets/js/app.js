// App.js - FinTech Demo V1

// Fonction de déconnexion globale (disponible sur toutes les pages)
function logout() {
    fetch('../api/auth/logout.php').then(() => {
        window.location.href = 'index.php';
    });
}

// Gestion du formulaire de connexion
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const messageDiv = document.getElementById('message');
            try {
                const response = await fetch('../api/auth/login.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.textContent = result.message;
                    messageDiv.style.display = 'block';
                }
            } catch (error) {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = 'Erreur de connexion';
                messageDiv.style.display = 'block';
            }
        });
    }

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const messageDiv = document.getElementById('message');
            try {
                const response = await fetch('../api/auth/register.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    messageDiv.className = 'alert alert-success';
                    messageDiv.textContent = 'Inscription réussie ! Redirection...';
                    messageDiv.style.display = 'block';
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.textContent = result.message || 'Erreur lors de l\'inscription';
                    messageDiv.style.display = 'block';
                }
            } catch (error) {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = 'Erreur serveur';
                messageDiv.style.display = 'block';
            }
        });
    }
});

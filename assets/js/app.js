// app.js - FinTech Demo V1
// Gestion du formulaire de connexion (index.php)
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
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
                messageDiv.textContent = 'Erreur de connexion au serveur';
                messageDiv.style.display = 'block';
            }
        });
    }
});

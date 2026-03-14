/**
 * pro.js - Pure Pro FinTech_Vulnerable Interactivity
 * Focus: Smooth transitions, subtle hovers, and clean feedback.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Brand Typing Effect
    const brandSub = document.getElementById('brand-subtitle');
    if (brandSub) {
        const text = brandSub.innerText;
        brandSub.innerText = '';
        let i = 0;
        const type = () => {
            if (i < text.length) {
                const char = text.charAt(i);
                brandSub.innerHTML += char === ' ' ? '&nbsp;' : char;
                i++;
                setTimeout(type, 50);
            }
        };
        setTimeout(type, 800);
    }

    // 2. Organic Mouse Parallax (Sophisticated)
    document.addEventListener('mousemove', (e) => {
        const cards = document.querySelectorAll('.pro-card, .hero-title-big, .hero-subtitle-big');
        const x = (window.innerWidth / 2 - e.pageX) / 80;
        const y = (window.innerHeight / 2 - e.pageY) / 80;

        cards.forEach((card, index) => {
            const factor = index % 2 === 0 ? 1 : 1.2;
            card.style.transform = `translate(${x * factor}px, ${y * factor}px)`;
        });
    });

    // 3. Staggered Column Entrance (Top Experience)
    const columns = document.querySelectorAll('.col-lg-4, .col-lg-3');
    columns.forEach((col, i) => {
        col.style.opacity = '0';
        col.style.transform = 'translateY(30px)';
        col.style.transition = 'all 1.2s cubic-bezier(0.22, 1, 0.36, 1)';
        setTimeout(() => {
            col.style.opacity = '1';
            col.style.transform = 'translateY(0)';
        }, 200 * i);
    });

    // 3. Smooth Form Scaling on Toggle
    window.toggleAuthMode = function () {
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const authTitle = document.getElementById('auth-title');
        const toggleBtn = document.getElementById('toggle-auth');
        const card = document.getElementById('auth-card');

        // Wow scaling effect
        card.style.transform = 'scale(0.98)';
        card.style.opacity = '0.8';

        setTimeout(() => {
            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
                authTitle.innerText = 'Connexion';
                toggleBtn.innerHTML = 'S\'inscrire <i class="bi bi-arrow-right small"></i>';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                authTitle.innerText = 'Inscription';
                toggleBtn.innerHTML = '<i class="bi bi-arrow-left small"></i> Déjà un compte ?';
            }
            card.style.transform = 'scale(1)';
            card.style.opacity = '1';
        }, 300);
    };

    // 4. Form Input Polish (Enhanced)
    const inputs = document.querySelectorAll('.form-control-pro');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            const label = input.closest('.form-group-pro').querySelector('.form-label-pro');
            if (label) {
                label.style.color = 'var(--pro-primary)';
                label.style.transform = 'translateX(5px)';
                label.style.fontWeight = '700';
            }
        });

        input.addEventListener('blur', () => {
            const label = input.closest('.form-group-pro').querySelector('.form-label-pro');
            if (label) {
                label.style.color = 'var(--pro-text-muted)';
                label.style.transform = 'translateX(0)';
                label.style.fontWeight = '500';
            }
        });
    });

    // 5. PRESENTATION MODE TOGGLE (Secret: Ctrl + Shift + E)
    // This allows the presenter to hide all vulnerabilities and show a perfectly professional SECURE platform.
    
    // Check initial state from localStorage
    if (localStorage.getItem('fintech_lab_mode') === 'true') {
        document.body.classList.add('show-lab');
    }

    document.addEventListener('keydown', (e) => {
        // Ctrl + Shift + E
        if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'e') {
            e.preventDefault(); // Prevent default browser behavior if any
            
            const isLabMode = document.body.classList.toggle('show-lab');
            localStorage.setItem('fintech_lab_mode', isLabMode);
            
            // Optional: Subtle visual feedback that mode changed (opacity pulse on body)
            document.body.style.opacity = '0.8';
            setTimeout(() => { document.body.style.opacity = '1'; }, 150);
            
            console.log(`[FINTECH] Lab Mode: ${isLabMode ? 'ON' : 'OFF'}`);
        }
    });

    // Alternative: Secret Triple Click on the Brand Name
    const brandElements = document.querySelectorAll('.pro-brand');
    brandElements.forEach(brand => {
        let clickCount = 0;
        let clickTimer;
        
        brand.addEventListener('click', (e) => {
            // Only capture clicks exactly on the text, not preventing default navigation if it's a link
            clickCount++;
            
            if (clickCount === 1) {
                clickTimer = setTimeout(() => {
                    clickCount = 0;
                }, 1000); // Reset after 1 second
            } else if (clickCount >= 3) {
                e.preventDefault(); // Stop navigation on triple click
                clearTimeout(clickTimer);
                clickCount = 0;
                
                const isLabMode = document.body.classList.toggle('show-lab');
                localStorage.setItem('fintech_lab_mode', isLabMode);
                
                document.body.style.opacity = '0.8';
                setTimeout(() => { document.body.style.opacity = '1'; }, 150);
                
                console.log(`[FINTECH] Lab Mode (Triple Click): ${isLabMode ? 'ON' : 'OFF'}`);
            }
        });
    });
});

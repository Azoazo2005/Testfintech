/**
 * pro.js - Pure Pro FinTech Interactivity
 * Focus: Smooth transitions, subtle hovers, clean feedback + Mode Présentation.
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

    // 2. Organic Mouse Parallax
    document.addEventListener('mousemove', (e) => {
        const cards = document.querySelectorAll('.pro-card:not(.no-parallax), .hero-title-big, .hero-subtitle-big');
        const x = (window.innerWidth / 2 - e.pageX) / 80;
        const y = (window.innerHeight / 2 - e.pageY) / 80;
        cards.forEach((card, index) => {
            const factor = index % 2 === 0 ? 1 : 1.2;
            card.style.transform = `translate(${x * factor}px, ${y * factor}px)`;
        });
    });

    // 3. Staggered Column Entrance
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

    // 4. Smooth Form Scaling on Auth Toggle
    window.toggleAuthMode = function () {
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const authTitle = document.getElementById('auth-title');
        const toggleBtn = document.getElementById('toggle-auth');
        const card = document.getElementById('auth-card');

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

    // 5. Form Input Polish
    const inputs = document.querySelectorAll('.form-control-pro');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            const group = input.closest('.form-group-pro');
            if (!group) return;
            const label = group.querySelector('.form-label-pro');
            if (label) {
                label.style.color = 'var(--pro-primary)';
                label.style.transform = 'translateX(5px)';
                label.style.fontWeight = '700';
            }
        });
        input.addEventListener('blur', () => {
            const group = input.closest('.form-group-pro');
            if (!group) return;
            const label = group.querySelector('.form-label-pro');
            if (label) {
                label.style.color = 'var(--pro-text-muted)';
                label.style.transform = 'translateX(0)';
                label.style.fontWeight = '500';
            }
        });
    });

    // ============================================================
    // 6. MODE PRÉSENTATION — Bascule Secrète LAB / PRO
    //    Cheat code 1 : Ctrl + Maj + E
    //    Cheat code 2 : Triple clic rapide sur le logo FINTECH
    //    Persistance  : localStorage ('fintech_lab_mode')
    // ============================================================

    const LAB_KEY = 'fintech_lab_mode';

    // Restaurer l'état sauvegardé immédiatement (sans flash)
    if (localStorage.getItem(LAB_KEY) === 'true') {
        document.body.classList.add('show-lab');
    }

    function triggerLabToggle() {
        const isLab = document.body.classList.toggle('show-lab');
        localStorage.setItem(LAB_KEY, isLab ? 'true' : 'false');
        // Flash visuel de confirmation
        document.body.classList.add('lab-toggle-flash');
        setTimeout(() => document.body.classList.remove('lab-toggle-flash'), 650);
    }

    // Cheat code 1 : Ctrl + Maj + E
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.shiftKey && (e.key === 'E' || e.key === 'e')) {
            e.preventDefault();
            triggerLabToggle();
        }
    });

    // Cheat code 2 : Triple clic rapide sur le logo .pro-brand
    const brand = document.querySelector('.pro-brand');
    if (brand) {
        let clickCount = 0;
        let clickTimer = null;
        brand.addEventListener('click', (e) => {
            e.preventDefault();
            clickCount++;
            if (clickTimer) clearTimeout(clickTimer);
            clickTimer = setTimeout(() => {
                if (clickCount >= 3) triggerLabToggle();
                clickCount = 0;
            }, 500);
        });
    }

});

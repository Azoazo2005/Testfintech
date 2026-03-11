# Corrections V2 - Guide de sécurisation

## À implémenter en V2

### 1. Injection SQL → Requêtes préparées
### 2. MD5 → password_hash() / password_verify()
### 3. IDOR → Vérification d'autorisation stricte
### 4. Race Condition → Transactions SQL (BEGIN, COMMIT, ROLLBACK)
### 5. XSS → Échappement HTML systématique
### 6. CSRF → Tokens de protection
### 7. Configuration → Masquer les erreurs en production
### 8. Logging → Système de logs robuste

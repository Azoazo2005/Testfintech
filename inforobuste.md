# Journal des Modifications — Application FINTECH

Dernière mise à jour : 14 mars 2026

---

## 1. Mode Présentation (Bascule Secrète LAB / PRO)

Un système de bascule caché permet de révéler les modules de hacking uniquement à la demande du présentateur.

### Cheat codes
- **`Ctrl + Maj + E`** — raccourci clavier global
- **Triple clic rapide** sur le logo **FINTECH** en haut à gauche

L'état est mémorisé via `localStorage` (`fintech_lab_mode`) et persiste entre les pages et les rafraîchissements. Un effet d'assombrissement confirme chaque bascule.

### Architecture
- **Classes CSS** : `.lab-only` (masqué par défaut) / `body.show-lab .lab-only` (visible en mode Lab) / `.pro-only`
- **Script JS** : `public/assets/js/pro.js` — détection clavier, triple clic, persistance localStorage

### Fichiers modifiés
| Fichier | Élément masqué en mode Présentation |
|---|---|
| `public/assets/css/style.css` | Ajout des classes utilitaires + animation flash |
| `public/assets/js/pro.js` | Logique de bascule complète |
| `public/index.php` | Bouton LAB GUIDE (navbar) + bloc SQLi |
| `public/dashboard.php` | Bouton LAB GUIDE (navbar) + bloc XSS |
| `public/transfer.php` | Bouton LAB GUIDE (navbar) + colonne LAB entière |
| `public/admin.php` | Badge "Surveillance Active" (remplace "SÉCURITÉ ACTIVE") |

> **Consigne de base** : Le code backend PHP est intact. Les failles SQLi, XSS et IDOR fonctionnent exactement comme avant, quel que soit le mode visuel.

---

## 2. Ajout du lien TRANSFERT dans toutes les navbars

Un lien **TRANSFERT** a été ajouté dans les barres de navigation pour permettre un accès direct à la page de transfert depuis n'importe quelle page.

| Fichier | Liens de navigation ajoutés |
|---|---|
| `public/dashboard.php` | TRANSFERT (avant DÉCONNEXION) |
| `public/admin.php` | DASHBOARD + TRANSFERT (avant DÉCONNEXION) |
| `public/transfer.php` | Déjà sur la page — lien DASHBOARD présent |

---

## 3. Renommage : FINTECH ROBUSTE → FINTECH

Le nom de l'application a été standardisé à **FINTECH** partout (navbars, titres, footers).

| Fichier | Occurrences modifiées |
|---|---|
| `public/index.php` | Navbar, titre hero, copyright footer |
| `public/dashboard.php` | Navbar, copyright footer |
| `public/transfer.php` | Navbar |
| `public/admin.php` | Navbar, copyright footer |
| `public/register.php` | Logo, copyright footer |
| `public/lab.php` | Navbar (`FINTECH LAB`), copyright footer |

> Les mentions techniques comme *"Défense Robuste"*, *"Standard Robuste"* ou *"le système est ROBUSTE"* ont été conservées car elles décrivent des concepts de sécurité, pas le nom de l'app.

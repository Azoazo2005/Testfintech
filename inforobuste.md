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

---

## 4. Correction de la Base de Données & Hachage des mots de passe

Afin d'assurer que l'application soit déployable immédiatement après un clonage du dépôt git sans erreur de connexion ou de requêtes, le fichier d'initialisation SQL (`sql/sqlrobuste.sql`) et la logique de transaction ont été corrigés :

- **Hachage des mots de passe par défaut** : Les requêtes d'insertion dans le fichier SQL pour les comptes par défaut (`admin`, `alice`, `bob`, `victim`) hachent désormais directement leur mot de passe via Bcrypt. Ceci corrige le bug de "mot de passe incorrect" où les anciens mots de passe étaient en texte brut mais le script de connexion PHP vérifiait avec `password_verify()`. Le login admin fonctionne maintenant avec `admin123` et pour les autres `password`.
- **Ajout des colonnes manquantes** : Les colonnes `fee` et `payment_method` manquantes dans la table `transactions_v2` (qui causaient des crashs sur le tableau de bord Admin) ont été proprement ajoutées dans le schéma SQL.
- **Requêtes de Transfert Compatibles** : Les requêtes d'insertion liées aux transferts (`core/Transfer.php` et `api/transfer/process.php`) ont été mises à jour pour respecter exactement les colonnes attendues par `transactions_v2`, ce qui a résolu l'erreur "Destinataire introuvable".

---

## 5. Alignements Visuels "PRO" (Formulaire de Transfert)

Des correctifs CSS ont été apportés pour parfaire l'apparence "PRO" :
- Résolution du problème de chevauchement du texte sur l'insigne FCFA dans le champ montant grâce à la création d'un prefix modificateur `.has-prefix` dans `style.css`.
- Remplacement du groupe d'input standard de Bootstrap sur la page de transfert (`transfer.php`) par une iconographie et un alignement conformes à l'esthétique générale de la plateforme (suppression de l'arrière-plan gris inutile).

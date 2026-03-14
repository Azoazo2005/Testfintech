# Informations de Mise à Jour (Mode Présentation)

Ce document retrace les dernières modifications apportées à l'interface de l'application **FINTECH** afin de permettre un usage double (démonstration "Corporate" et démonstration "Vulnerable/Academy").

## Objectif Principal
Rendre l'application présentable visuellement comme un produit sécurisé et abouti lors de présentations, tout en gardant accessible tout l'écosystème de vulnérabilités pour les démonstrations techniques (Labo). Le nom de l'application a été standardisé à **FINTECH** partout au lieu de l'ancien `FINTECH_VULNERABLE`.

## Fonctionnalité : Le Mode "Présentation" (Bascule Secrète)

Un système de bascule (toggle) caché a été implémenté pour révéler les modules de hacking uniquement à la demande du présentateur.

### Comment basculer entre les vues ?
Le mode Lab (qui affiche les encarts de vulnérabilités et les astuces) est masqué par défaut. Pour le révéler, vous avez deux "cheat codes" :
1. **Raccourci Clavier Global** : Appuyez simultanément sur `Ctrl + Maj + E`.
2. **Clics Multiples** : Cliquez rapidement **3 fois d'affilée** sur le logo **FINTECH** situé en haut à gauche de la barre de navigation.

Effectuer l'une de ces actions révèlera instantanément (avec un effet assombri temporaire) :
- Les encarts "Security Laboratory" sur la page d'accueil.
- Les blocs "Security Insights" sur le Dashboard.
- Les guides "Scénarios d'exploitation" sur la page de Transfert.
- Le badge "Surveillance Active" dans le panneau d'administration.
- Le lien "LAB GUIDE" dans la barre de navigation.

_Note : Le comportement est sauvegardé dans le navigateur (`localStorage`). Si vous activez le mode Lab, il le restera même après un rafraîchissement ou un changement de page, jusqu'à ce que vous refassiez le raccourci pour le désactiver._

## Fichiers Modifiés 

Cette architecture repose sur des classes CSS (`.lab-only` et `.pro-only`) et un script JS dédié.

*   `public/assets/css/style.css` : Ajout des classes utilitaires pour masquer/afficher les éléments selon l'état du `<body>` (`body.show-lab`).
*   `public/assets/js/pro.js` : Logique de détection de frappes clavier, de détection de triple clic et de gestion de la persistance via `localStorage`.
*   `public/index.php`, `public/dashboard.php`, `public/admin.php`, `public/transfer.php`, `public/register.php`, `public/lab.php` : 
    *   Renommage mondial de "FINTECH_VULNERABLE" vers "FINTECH".
    *   Application des classes `.lab-only` sur les conteneurs éducatifs (les blocs d'explications et astuces de hacking).
    *   S'assurer que le bouton "TRANSFERT" reste globalement visible indépendamment du mode sélectionné.

## Consigne de Base
**Le code backend (PHP) et la structure même de la plateforme de vulnérabilité sont restés exactement identiques.** Aucun filtre n'a été rajouté, les failles XSS, IDOR et SQLi fonctionnent toujours de la même manière, qu'on soit visuellement en mode Présentation (masqué) ou non.

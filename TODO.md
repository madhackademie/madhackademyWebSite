# TODO — madhackademyWebSite

> Dernière mise à jour : 21 juin 2026  
> Projet : site vitrine FlashDev + MadHackAdemy

## Site en production

| Page | URL |
|------|-----|
| Accueil FlashDev | [https://gameopenmoney.com/](https://gameopenmoney.com/) |
| Centre de formation | [https://gameopenmoney.com/centre-formation.html](https://gameopenmoney.com/centre-formation.html) |

---

## Tâches prioritaires

Ces tâches débloquent la mise en ligne ou corrigent des problèmes visibles pour les visiteurs.

### Contenu (bloquant publication centre-formation)

- [ ] **P1** — Rédiger l'accroche hero de `centre-formation.html` (1–2 phrases, cible + promesse)
- [ ] **P1** — Compléter la section « Qui suis-je ? » (bio, parcours, placeholders restants)
- [ ] **P1** — Remplir la méthode SITE + SOFT (sous-titre + 3 lignes par pilier)
- [ ] **P1** — Rédiger la roadmap centre-formation (4 étapes : titres, descriptions, durées)
- [ ] **P1** — Définir les 3 offres boutique (noms, contenu, prix, CTA)
- [ ] **P1** — Remplacer `[MadHackAdemy / LOGO]` et le footer `[TON NOM / CENTRE DE FORMATION]`

### Liens & mise en ligne

- [ ] **P1** — Remplacer tous les liens `#` sur `index.html` (GitHub, Twitch, YouTube, achat premium)
- [x] **P1** — Configurer l'hébergement statique → [gameopenmoney.com](https://gameopenmoney.com/)
- [x] **P2** — Vérifier que la navigation inter-pages fonctionne en production (`/` ↔ `/centre-formation.html`)

### Corrections techniques urgentes

- [ ] **P1** — Fermer la balise `<div class="pt-24">` manquante sur `index.html`
- [ ] **P2** — Normaliser le chemin image MiniPoulpe : `/Image/MiniPoulpeDicord.png` (au lieu de `\`)
- [ ] **P2** — Harmoniser le discours Lua vs C++ sur `index.html` (roadmap = C++/Raylib)

---

## Backlog

Tâches utiles mais non bloquantes — à traiter après les priorités.

### Contenu & éditorial

- [ ] Ajouter une page ou section FAQ (méthode, prérequis, durée des formations)
- [ ] Rédiger les textes légaux (mentions légales, CGV boutique)
- [ ] Préparer des témoignages / preuves sociales pour la page centre-formation
- [ ] Aligner la roadmap centre-formation avec celle de FlashDev (`index.html`) ou expliquer la différence

### Technique & UX

- [ ] Implémenter un countdown JS dynamique pour le stream du samedi (`index.html`)
- [ ] Ajouter un favicon et des meta SEO (description, Open Graph, Twitter Card)
- [ ] Extraire les styles communs (charte Nintendo) dans un fichier CSS partagé
- [ ] Remplacer Tailwind CDN par une build locale (perf + offline)
- [ ] Ajouter un menu mobile responsive (hamburger) sur les deux pages
- [ ] Corriger le titre `<title>` : `[MadHackAdemy]` → nom définitif

### Projet & maintenance

- [ ] Rédiger un `README.md` (description, preview locale, déploiement)
- [ ] Structurer un dossier `assets/` ou `css/` si le site grossit
- [ ] Configurer analytics (Plausible, GA4…) si souhaité
- [ ] Mettre en place un workflow de preview (PR previews Netlify/Vercel)
- [ ] Ajouter des tests de régression visuelle ou lint HTML (optionnel)

### Évolutions produit

- [ ] Intégrer un système de paiement pour les decks premium (Stripe, Gumroad…)
- [ ] Page dédiée par offre boutique avec landing optimisée conversion
- [ ] Formulaire de contact ou inscription newsletter
- [ ] Version anglaise du site (i18n)

---

## Légende priorités

| Tag | Signification |
|-----|---------------|
| **P1** | Critique — à faire en premier |
| **P2** | Important — rapidement après P1 |
| *(backlog)* | Amélioration — quand le site est en ligne et le contenu rempli |

---

## État rapide du projet

| Page | Avancement estimé |
|------|---------------------|
| `index.html` (FlashDev) | ~80 % — contenu OK, liens et détails à finaliser |
| `centre-formation.html` | ~30 % — structure solide, contenu à rédiger |

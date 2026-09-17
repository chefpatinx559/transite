# NETSPRING — Journal des améliorations

## Modifications appliquées

### PROBLÈME 1 — SEO / Crawlabilité

| Fichier | Modification |
|---|---|
| `public/robots.txt` | Corrigé l'URL du sitemap : `netspring.ci` → `netspring.business` |
| `resources/views/app.blade.php` | Remplacé l'image OG Vecteezy par `asset('og-image.png')` (à créer) |
| `resources/views/app.blade.php` | Ajouté le bloc JSON-LD Schema.org `LocalBusiness` (Google Rich Results) |

**Ce qui était déjà en place et fonctionne :**
- `<Head>` Inertia.js utilisé dans toutes les pages principales (title + meta description)
- `@inertiaHead` présent dans `app.blade.php`
- Open Graph + Twitter Card dans `app.blade.php`
- Les données produits/formations/articles sont passées via `Inertia::render()` (server-side) — pas de fetch JS post-chargement
- Sitemap dynamique via `SitemapController` à `/sitemap.xml`

---

### PROBLÈME 2 — Image Hero

| Fichier | Modification |
|---|---|
| `resources/js/Pages/Home.vue` | Supprimé `DEFAULT_COVER` (URL Vecteezy) — plus de dépendance à un CDN externe |
| `resources/js/Components/BlogPreview.vue` | Fallback gradient NETSPRING (`from-[#0D0D0D]`) au lieu de la photo stock |
| `resources/views/app.blade.php` | OG image ne pointe plus vers Vecteezy |

Le bloc visuel Hero côté droit était déjà un gradient — il n'a pas été modifié.

---

### PROBLÈME 3 — Preuve sociale

| Fichier | Modification |
|---|---|
| `resources/js/Components/SocialProof.vue` | **Créé** — stats animés (IntersectionObserver) + cards témoignages |
| `routes/web.php` | Ajouté `testimonials` dans `Inertia::render('Home', [...])` |
| `resources/js/Pages/Home.vue` | Remplacé les sections stats + témoignages inline par `<SocialProof :testimonials="testimonials" />` |

Les témoignages viennent maintenant du controller Laravel. Pour en ajouter/modifier,
éditer uniquement le tableau `$testimonials` dans `routes/web.php` (route `GET /`).

---

### PROBLÈME 4 — Blog sous-exploité

| Fichier | Modification |
|---|---|
| `resources/js/Components/BlogPreview.vue` | **Créé** — section blog extraite de `Home.vue`, accepte `latestPosts` en prop |
| `resources/js/Pages/Home.vue` | Remplacé la section blog inline par `<BlogPreview :latest-posts="latestPosts" />` |

Les 4 derniers articles sont déjà passés via `Inertia::render()` — aucun fetch JS.
Le modèle `Post` existait déjà avec les champs nécessaires.

---

## Fichiers créés

```
resources/js/Components/SocialProof.vue   — Stats + témoignages (réutilisable)
resources/js/Components/BlogPreview.vue   — Aperçu blog (réutilisable)
AMELIORATIONS.md                          — Ce fichier
```

## Fichiers modifiés

```
public/robots.txt
resources/views/app.blade.php
resources/js/Pages/Home.vue
routes/web.php
```

---

## TODOs restants

### Priorité haute
- [ ] **og-image.png** — Créer `public/og-image.png` (1200×630px) avec une vraie photo
  (équipe, locaux Bouaké, marchandises). Format WebP ou JPEG optimisé.
- [ ] **Photo hero** — Remplacer le gradient hero (bloc "Visuel droit" dans `Home.vue`)
  par une photo authentique : `resources/images/hero-netspring.webp` (800×600px),
  importée via Vite.

### Priorité moyenne
- [ ] **Vrais témoignages** — Remplacer les 3 placeholders dans `routes/web.php`
  (tableau `$testimonials`) par de vrais clients avec photos.
  Format avatar : `resources/images/temoignage-xxx.webp` importée → `Storage::url()`.
- [ ] **Images articles blog** — Ajouter une couverture réelle à chaque article
  depuis l'admin (`storage/app/public/posts/`). Les articles sans image affichent
  un gradient de remplacement.
- [ ] **Email SMTP** — Configurer `MAIL_MAILER=smtp` avec Gmail ou Brevo
  pour les vraies confirmations de commande et d'inscription formation.

### Priorité basse
- [ ] **Newsletter Brevo** — Intégration API Brevo dans `NewsletterController`.
- [ ] **Admin URL sécurisée** — `/admin` (SPA React) est accessible sans auth côté URL.
  Ajouter un middleware ou une auth préalable pour masquer l'interface.
- [ ] **Schema.org par page** — Ajouter des types plus précis dans les pages :
  `Course` pour les formations, `Product` pour la boutique, `BlogPosting` pour les articles.

---

## Commandes à lancer après les modifications

```bash
# Build local (vérifier qu'il n'y a pas d'erreur)
npm run build

# Régénérer le sitemap (si SitemapController utilise un cache)
php artisan sitemap:generate    # si la commande existe
# ou simplement visiter https://netspring.business/sitemap.xml pour forcer

# Déployer en production
git add -A
git commit -m "feat: SEO, SocialProof, BlogPreview components + cleanup"
git push origin main            # déclenche le CI/CD GitHub Actions

# Sur le serveur (automatique via deploy.yml, mais si manuel)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Architecture des composants Vue (après refactoring)

```
Home.vue
├── AppLayout.vue
├── [sections inline] Hero, TrustMarquee, Services, WhyNetspring, Process, ImportFormation, CTA
├── SocialProof.vue        ← NOUVEAU — reçoit :testimonials du controller
│   ├── Stats animés (IntersectionObserver)
│   └── Témoignages cards
└── BlogPreview.vue        ← NOUVEAU — reçoit :latest-posts du controller
    └── Grid d'articles avec fallback gradient
```

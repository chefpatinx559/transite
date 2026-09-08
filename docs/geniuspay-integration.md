# Intégration GeniusPay — NETSPRING

Paiement mobile et carte bancaire via l'API GeniusPay.  
Moyens supportés : **Wave · Orange Money · MTN MoMo · Moov · Carte Visa/Mastercard**

---

## Flux de paiement

```
1. Client remplit le formulaire checkout
2. POST /checkout  →  Laravel crée la commande (status: pending)
3. Laravel appelle l'API GeniusPay  →  reçoit checkout_url
4. Client redirigé vers la page GeniusPay (choisit son moyen de paiement)
5. Client paie
6. GeniusPay envoie webhook POST /webhooks/geniuspay  (signé HMAC-SHA256)
7. Laravel vérifie la signature  →  commande marquée paid
8. GeniusPay redirige le client vers /paiement/succes?order=TOKEN
```

---

## Configuration `.env`

```env
# ── GeniusPay ──────────────────────────────────────────────────
GENIUSPAY_API_KEY=<votre-cle-publique-live>
GENIUSPAY_API_SECRET=<votre-cle-secrete-live>
GENIUSPAY_WEBHOOK_SECRET=<votre-secret-webhook-live>
GENIUSPAY_ENV=live
```

> **Sandbox** : remplacer `pk_live_` par `pk_sandbox_` et `sk_live_` par `sk_sandbox_`  
> Les clés sont disponibles dans **Dashboard GeniusPay → Paramètres → API**

---

## Fichiers clés

| Fichier | Rôle |
|---------|------|
| `app/Services/GeniusPayService.php` | Appels API (création paiement, récupération, vérification signature) |
| `app/Http/Controllers/CheckoutController.php` | Crée la commande, appelle GeniusPay, gère les redirections |
| `app/Http/Controllers/GeniusPayWebhookController.php` | Reçoit et traite les webhooks |
| `config/services.php` | Config GeniusPay (`api_key`, `api_secret`, `webhook_secret`) |
| `resources/js/Pages/Checkout.vue` | Formulaire checkout simplifié |
| `resources/js/Pages/Commande/Confirmation.vue` | Page succès après paiement |
| `resources/js/Pages/Commande/PaymentError.vue` | Page d'échec avec lien WhatsApp |

---

## Routes

```
POST  /checkout                        →  CheckoutController@process
GET   /paiement/succes?order={token}   →  CheckoutController@paymentSuccess
GET   /paiement/echec?order={token}    →  CheckoutController@paymentError
POST  /webhooks/geniuspay              →  GeniusPayWebhookController@handle
GET   /commande/confirmation/{token}   →  CheckoutController@confirmation
```

> Le webhook `/webhooks/geniuspay` est **exclu du middleware CSRF** (`bootstrap/app.php`).

---

## Configuration webhook dans le Dashboard GeniusPay

1. Aller dans **Dashboard → Webhooks → Créer un webhook**
2. Renseigner :
   - **URL** : `https://ton-domaine.ci/webhooks/geniuspay`
   - **Événements** : `payment.success`, `payment.failed`
3. Copier le **secret webhook** (`whsec_live_...`) et le mettre dans `.env`

> Le secret est affiché **une seule fois** à la création. Le sauvegarder immédiatement.

---

## Sécurité webhook

Chaque webhook entrant est vérifié :

```php
// Signature : HMAC-SHA256(timestamp + "." + json_payload, webhook_secret)
$data     = $timestamp . '.' . $rawPayload;
$expected = hash_hmac('sha256', $data, $webhookSecret);
hash_equals($expected, $signature);  // comparaison timing-safe

// Anti-replay : timestamp doit être < 5 minutes
abs(time() - $timestamp) <= 300;
```

Headers vérifiés :
- `X-Webhook-Signature` — signature HMAC-SHA256
- `X-Webhook-Timestamp` — timestamp Unix de l'envoi
- `X-Webhook-Event` — type d'événement (`payment.success`, etc.)

---

## Statuts de commande

| Statut `orders.status` | Déclencheur |
|------------------------|-------------|
| `pending` | Commande créée, en attente de paiement |
| `paid` | Webhook `payment.success` reçu et vérifié |
| `cancelled` / `refunded` | Manuel via l'admin |

| Statut `orders.payment_status` | Valeur |
|--------------------------------|--------|
| `unpaid` | Après création commande |
| `paid` | Après webhook success |
| `failed` | Après redirection `/paiement/echec` ou webhook failed |

---

## Champs `orders` utilisés par GeniusPay

| Colonne | Contenu |
|---------|---------|
| `payment_method` | Toujours `geniuspay` |
| `payment_ref` | Référence GeniusPay : `MTX-XXXXXXXXXX` |
| `token` | Token interne (40 chars) — lien commande ↔ webhook via `metadata.order_token` |

---

## Metadata envoyées à GeniusPay

```json
{
  "order_id":     123,
  "order_token":  "abcdef1234567890abcdef1234567890abcdef12",
  "order_number": "NS-2026-000001"
}
```

Ces métadonnées sont retournées dans le webhook pour identifier la commande.  
Priorité de lookup : `order_token` → `payment_ref`.

---

## Tests en sandbox

1. Mettre les clés `pk_sandbox_` / `sk_sandbox_` / `whsec_sandbox_` dans `.env`
2. Passer une commande sur `http://127.0.0.1:8000/boutique`
3. Sur la page GeniusPay sandbox, utiliser les numéros de test fournis dans leur documentation
4. Vérifier les logs Laravel : `storage/logs/laravel.log`
5. Vérifier le statut de la commande dans l'admin (`/admin/commandes`)

Pour tester le webhook en local, utiliser [ngrok](https://ngrok.com) :
```bash
ngrok http 8000
# → URL publique : https://xxxx.ngrok.io
# Configurer dans GeniusPay : https://xxxx.ngrok.io/webhooks/geniuspay
```

---

## Passage en production

- [ ] Remplacer les clés sandbox par les clés live dans `.env`
- [ ] Mettre à jour l'URL webhook dans le Dashboard GeniusPay avec le domaine de production
- [ ] Vérifier que `APP_URL` dans `.env` correspond au domaine de production (pour `success_url` / `error_url`)
- [ ] S'assurer que le storage symlink est actif : `php artisan storage:link`
- [ ] Vérifier les logs après les premières transactions live

---

## Points d'attention

- **Ne jamais exposer** `GENIUSPAY_API_SECRET` et `GENIUSPAY_WEBHOOK_SECRET` côté client
- Le montant est **toujours recalculé depuis la BDD** dans `CheckoutController@process` — jamais depuis le formulaire client
- Si GeniusPay est inaccessible au moment du paiement, la commande reste en `pending` et l'admin voit une entrée sans `payment_ref` — contacter le client manuellement
- Le lien de checkout GeniusPay **expire après 24h**

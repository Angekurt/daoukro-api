# Déploiement API Daoukro — Laravel sur cPanel / CloudLinux

**Projet :** `api-daoukro`
**Serveur :** LWS mutualisé — CloudLinux + CageFS + Varnish (`fastestcache`)
**URL de production :** `https://api-daoukro.akdev.tech/api/v1`
**Date de déploiement :** Juillet 2026

---

## 1. Fiche technique du serveur

| Élément | Valeur |
|---|---|
| Utilisateur cPanel | `c2613905c` |
| Home | `/home/c2613905c` |
| Racine projet | `/home/c2613905c/public_html/api-daoukro` |
| DocumentRoot du sous-domaine | `/home/c2613905c/public_html/api-daoukro/public` |
| Base de données | `c2613905c_daoukro_db` |
| Utilisateur MySQL | `c2613905c_akdev` |
| SGBD | MariaDB 11.4.10 |
| PHP CLI par défaut | 8.2.31 ❌ (insuffisant) |
| PHP utilisé | **8.3.31** via `/opt/alt/php83/usr/bin/php` |
| Composer | `/usr/local/bin/composer` |

> ⚠️ **Piège n°1 — le binaire PHP.**
> Laravel 12 exige PHP ≥ 8.3, mais le `php` du terminal cPanel est en 8.2.
> Sous CloudLinux, les binaires alternatifs **ne sont pas** dans `/opt/cpanel/ea-phpXX/` (masqués par CageFS) mais dans `/opt/alt/phpXX/usr/bin/php`.
>
> Toujours préfixer les commandes artisan :
> ```bash
> /opt/alt/php83/usr/bin/php artisan <commande>
> ```

---

## 2. Préparation de la base de données

### 2.1 Rendre le dump SQL ré-exécutable (idempotent)

Un dump HeidiSQL/phpMyAdmin brut échoue au 2ᵉ import :

| Erreur | Cause |
|---|---|
| `#1050 Table already exists` | `CREATE TABLE` sans `IF NOT EXISTS` |
| `#1062 Duplicate entry` | `INSERT INTO` sur une ligne déjà présente |
| `#1215 / errno 150 Cannot add foreign key` | table parente pas encore créée (ordre alphabétique du dump) |

**Corrections à appliquer au fichier `.sql` :**

```sql
-- ============ EN-TÊTE ============
SET NAMES utf8mb4;
SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET FOREIGN_KEY_CHECKS=0;          -- neutralise l'ordre des FK
SET @OLD_TIME_ZONE=@@TIME_ZONE;
SET TIME_ZONE='+00:00';
```

```sql
-- ============ TABLES ============
-- IF NOT EXISTS => pas d'erreur si la table est déjà là
CREATE TABLE IF NOT EXISTS `documents_prestataires` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prestataire_id` bigint unsigned NOT NULL,
  ...
  CONSTRAINT `documents_prestataires_prestataire_id_foreign`
    FOREIGN KEY (`prestataire_id`) REFERENCES `prestataires` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

```sql
-- ============ DONNÉES ============
-- ON DUPLICATE KEY UPDATE => met à jour au lieu de planter
INSERT INTO `villes` (`id`, `nom`, `region`, `pays`, `is_active`) VALUES
  (1, 'Daoukro', 'Iffou', 'Côte d\'Ivoire', 1)
ON DUPLICATE KEY UPDATE
  `nom`=VALUES(`nom`), `region`=VALUES(`region`),
  `pays`=VALUES(`pays`), `is_active`=VALUES(`is_active`);
```

```sql
-- ============ PIED ============
SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE,'system');
SET FOREIGN_KEY_CHECKS=1;
```

> **Pourquoi `ON DUPLICATE KEY UPDATE` et pas `REPLACE INTO` ?**
> `REPLACE` fait un `DELETE` + `INSERT`. Sur une table parente (ex. `villes`), le `DELETE` déclenche les `ON DELETE CASCADE` et **efface les enfants** (`pharmacies`, `services_publics`). `ON DUPLICATE KEY UPDATE` fait un vrai `UPDATE`, sans suppression.

> **Exclure `id` de la clause UPDATE** — inutile et risqué (il sert de clé de correspondance).

### 2.2 Import

phpMyAdmin (cPanel) → base `c2613905c_daoukro_db` → onglet *Importer* → fichier `.sql` corrigé.

### 2.3 Créer et rattacher l'utilisateur MySQL

**cPanel > Bases de données MySQL :**

1. *Utilisateurs MySQL actuels* → créer `c2613905c_akdev`
2. **Mot de passe sans caractères spéciaux ambigus** : éviter `@ $ # " ' \` et les espaces — ils cassent le parsing du `.env` et des URL DSN.
3. *Ajouter un utilisateur à une base de données* → associer l'utilisateur à `c2613905c_daoukro_db` → cocher **ALL PRIVILEGES**

> ⚠️ **Piège n°2.** Créer l'utilisateur ne suffit pas : sans l'association explicite à la base, MySQL renvoie
> `SQLSTATE[HY000] [1045] Access denied for user '...'@'localhost' (using password: YES)`
> — message trompeur : ce n'est pas forcément le mot de passe qui est faux.

**Test isolé (hors Laravel) pour trancher :**

```bash
mysql -u c2613905c_akdev -p c2613905c_daoukro_db
```

| Résultat | Diagnostic |
|---|---|
| prompt `MariaDB [..]>` | identifiants OK → le problème vient du `.env` |
| `ERROR 1045` | mot de passe faux ou utilisateur non rattaché |

---

## 3. Configuration du sous-domaine

### 3.1 Création — cPanel > Domaines > Créer un domaine

| Champ | Valeur |
|---|---|
| Domaine | `api-daoukro.akdev.tech` |
| Partager le document root | **décoché** |
| **Document Root** | `/home/c2613905c/public_html/api-daoukro/public` |

> ⚠️ **Piège n°3 — LE plus critique.**
> Si le DocumentRoot pointe sur la **racine du projet** au lieu de `/public`, alors
> `https://.../.env`, `/composer.json`, `/vendor/`, `/storage/logs/laravel.log` deviennent **téléchargeables publiquement**.
> → Fuite des identifiants DB, de l'`APP_KEY`, du code source.

**Vérification obligatoire :**

```bash
curl -sSI https://api-daoukro.akdev.tech/.env
```

| Code | Signification |
|---|---|
| `403` / `404` | ✅ protégé |
| `200` | 🚨 **fuite critique** — corriger immédiatement |

### 3.2 Solution de secours (si le DocumentRoot est verrouillé)

`.htaccess` à la **racine du projet** :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Blocage explicite des fichiers sensibles
    RewriteRule ^(\.env|\.env\..*|composer\.(json|lock)|artisan|package\.json|.*\.zip)$ - [F,L]

    # Redirection de tout le trafic vers /public
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 3.3 DNS + SSL

```bash
dig +short api-daoukro.akdev.tech    # doit renvoyer l'IP du serveur
```

Si vide → **cPanel > Zone Editor** → ajouter un enregistrement `A` : `api-daoukro` → IP du serveur.

SSL : **cPanel > SSL/TLS Status** → cocher le sous-domaine → **Run AutoSSL**.

---

## 4. Forcer PHP 8.3 sur ce seul sous-domaine

CloudLinux propose **PHP Selector**, mais il s'applique à **tout le compte** — risqué si d'autres sites (WordPress, Moodle…) tournent sur le même hébergement.

**Solution ciblée** — en tête de `public/.htaccess` :

```apache
# PHP 8.3 (alt-php83) — uniquement pour ce vhost
AddHandler application/x-httpd-alt-php83___lsphp .php

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>
    ...
</IfModule>
```

Commande appliquée :

```bash
cd /home/c2613905c/public_html/api-daoukro/public
cp .htaccess .htaccess.bak
printf '# PHP 8.3 (alt-php83)\nAddHandler application/x-httpd-alt-php83___lsphp .php\n\n' \
  | cat - .htaccess > .htaccess.tmp && mv .htaccess.tmp .htaccess
```

Vérification :

```bash
curl -sSI https://api-daoukro.akdev.tech/
# 200 => PHP 8.3 actif
# 500 => handler incorrect
```

---

## 5. Le fichier `.env` de production

```env
APP_NAME=Daoukro
APP_ENV=production
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX=
APP_DEBUG=false
APP_URL=https://api-daoukro.akdev.tech

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=c2613905c_daoukro_db
DB_USERNAME=c2613905c_akdev
DB_PASSWORD=MotDePasseSansCaracteresSpeciaux

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.akdev.tech
SESSION_SECURE_COOKIE=true

SANCTUM_STATEFUL_DOMAINS=api-daoukro.akdev.tech,akdev.tech

CACHE_STORE=database
QUEUE_CONNECTION=database
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=mail.akdev.tech
MAIL_PORT=465
MAIL_SCHEME=smtps
MAIL_USERNAME=no-reply@akdev.tech
MAIL_PASSWORD=xxxxx
MAIL_FROM_ADDRESS="no-reply@akdev.tech"
MAIL_FROM_NAME="${APP_NAME}"
```

### Erreurs à ne jamais commettre

| Variable | Valeur dangereuse | Conséquence |
|---|---|---|
| `APP_ENV` | `local` | mode dev en production |
| `APP_DEBUG` | `true` | **la page d'erreur affiche le contenu du `.env`** : mot de passe DB, `APP_KEY`, clés API |
| `APP_URL` | `http://localhost` | `asset()`, `url()`, liens de reset password, images stockées → tous cassés |
| `SESSION_DOMAIN` | `.tondomaine.ci` (placeholder copié) | cookies émis sur le mauvais domaine → auth impossible |
| `DB_*` | lignes commentées `#` | Laravel retombe sur les valeurs par défaut → `Access denied` |

> ⚠️ **Piège n°4.** Ne jamais copier-coller un `.env` d'exemple sans remplacer **tous** les placeholders. Vérification :
> ```bash
> grep -E "tondomaine|example\.com|localhost|CHANGEME" .env
> ```
> Doit ne rien renvoyer.

---

## 6. Séquence de déploiement complète

```bash
# ── 0. Se placer dans le projet ────────────────────────────────
cd /home/c2613905c/public_html/api-daoukro

# ── 1. Nettoyage sécurité ──────────────────────────────────────
rm -f daoukro-api.zip           # une archive dans le webroot = code source public
chmod 600 .env                  # lecture par le propriétaire uniquement
chmod -R 775 storage bootstrap/cache

# Repasser les permissions laxistes (777) à des valeurs saines
find app bootstrap config database public resources routes tests -type d -exec chmod 755 {} \;
find app bootstrap config database public resources routes tests -type f -exec chmod 644 {} \;

# ── 2. Dépendances (sans les paquets de dev) ───────────────────
composer install --no-dev --optimize-autoloader

# ── 3. Purge des caches (obligatoire après toute modif du .env) ─
/opt/alt/php83/usr/bin/php artisan config:clear
/opt/alt/php83/usr/bin/php artisan cache:clear
/opt/alt/php83/usr/bin/php artisan route:clear
/opt/alt/php83/usr/bin/php artisan view:clear

# ── 4. Vérifications ───────────────────────────────────────────
/opt/alt/php83/usr/bin/php artisan db:show          # connexion + liste des tables
/opt/alt/php83/usr/bin/php artisan migrate:status   # NE PAS migrer si le .sql est importé
/opt/alt/php83/usr/bin/php artisan route:list --path=api

# ── 5. Lien symbolique storage (photos, CNI, justificatifs) ────
/opt/alt/php83/usr/bin/php artisan storage:link

# ── 6. Optimisation production — EN DERNIER ────────────────────
/opt/alt/php83/usr/bin/php artisan config:cache
/opt/alt/php83/usr/bin/php artisan route:cache
/opt/alt/php83/usr/bin/php artisan view:cache
```

> ⚠️ **Piège n°5.** `config:cache` **fige** le `.env` dans `bootstrap/cache/config.php`.
> Toute modification ultérieure du `.env` sera **ignorée** tant que l'on n'a pas relancé `config:clear`.
> → Ne jamais lancer `config:cache` avant d'avoir validé toute la configuration.

> **Si le `.sql` a déjà été importé**, la table `migrations` est remplie : `migrate --force` ne rejouera rien. Lancer `migrate:status` avant, jamais `migrate:fresh` (⚠️ efface toutes les données).

---

## 7. Recette de validation

```bash
# 1. Le .env est-il protégé ?
curl -sSI https://api-daoukro.akdev.tech/.env
# attendu : 403 ou 404

# 2. Le vendor est-il protégé ?
curl -sSI https://api-daoukro.akdev.tech/vendor/autoload.php
# attendu : 403 ou 404

# 3. L'application répond-elle ?
curl -sSI https://api-daoukro.akdev.tech/
# attendu : 200

# 4. Une route métier renvoie-t-elle du JSON ?
curl -s https://api-daoukro.akdev.tech/api/v1/pharmacies
# attendu : {"success":true,"data":[...]}

# 5. Les erreurs sont-elles masquées ? (APP_DEBUG=false)
curl -s https://api-daoukro.akdev.tech/api/v1/route-inexistante
# attendu : page "Not Found" générique, SANS stack trace ni chemins serveur
```

---

## 8. Routes de l'API

Préfixe : **`/api/v1/`**

| Méthode | Route | Contrôleur |
|---|---|---|
| POST | `/api/v1/auth/register` | `AuthController@register` |
| POST | `/api/v1/auth/login` | `AuthController@login` |
| POST | `/api/v1/auth/logout` | `AuthController@logout` |
| GET | `/api/v1/auth/me` | `AuthController@me` |
| GET | `/api/v1/pharmacies` | `PharmacieController@index` |
| GET | `/api/v1/pharmacies/{id}` | `PharmacieController@show` |
| GET | `/api/v1/pharmacies/garde/actives` | `PharmacieController@gardesActives` |
| GET | `/api/v1/services-publics` | `ServicePublicController@index` |
| GET | `/api/v1/services-publics/{id}` | `ServicePublicController@show` |
| GET | `/api/v1/services-publics/categorie/{id}` | `ServicePublicController@parCategorie` |
| GET | `/api/v1/categories-services` | `CategorieServiceController@index` |
| GET | `/api/v1/artisans` | `ArtisanController@index` |
| GET | `/api/v1/artisans/{id}` | `ArtisanController@show` |
| GET | `/api/v1/artisans/metiers` | `ArtisanController@metiers` |
| GET | `/api/v1/actualites` · `/{id}` | `ActualiteController` |
| GET | `/api/v1/annonces` · `/{id}` | `AnnonceController` |
| GET | `/api/v1/hebergements` · `/{id}` | `HebergementController` |
| GET | `/api/v1/immobilier` · `/{id}` | `ImmobilierController` |
| GET | `/api/v1/urgences` · `/{id}` | `UrgenceController` |
| POST | `/api/v1/fcm-token` | `NotificationController@storeToken` |
| POST | `/api/v1/notifications/envoyer` | `NotificationController@envoyer` |

---

## 9. Côté application Flutter

```dart
// lib/config/api_config.dart
class ApiConfig {
  static const String baseUrl = 'https://api-daoukro.akdev.tech/api/v1';
}
```

Build de l'APK de production :

```bash
flutter clean
flutter pub get
flutter build apk --release
# → build/app/outputs/flutter-apk/app-release.apk
```

**Rappels Android :**
- Pas de `usesCleartextTraffic` nécessaire (HTTPS uniquement).
- La permission `INTERNET` doit être présente dans `AndroidManifest.xml`.
- FCM : la clé fournie initialement (`BL19…`) est une **clé VAPID Web Push**, pas une *server key* FCM. Pour FCM v1 il faut le **JSON de compte de service** Firebase, à placer hors du webroot (ex. `storage/app/firebase/service-account.json`) et à référencer via `FIREBASE_CREDENTIALS` dans le `.env`.

---

## 10. Récapitulatif sécurité

| # | Mesure | Statut |
|---|---|---|
| 1 | DocumentRoot pointé sur `/public` | ✅ |
| 2 | `.env` inaccessible en HTTP (403) | ✅ |
| 3 | `.env` en `chmod 600` | ✅ |
| 4 | Archive `.zip` supprimée du webroot | ✅ |
| 5 | Permissions ramenées de `777` à `755`/`644` | ✅ |
| 6 | `APP_DEBUG=false` · `APP_ENV=production` | ✅ |
| 7 | HTTPS + AutoSSL actif | ✅ |
| 8 | `SESSION_SECURE_COOKIE=true` | ✅ |
| 9 | `composer install --no-dev` | ✅ |
| 10 | Config / routes / vues mises en cache | ✅ |
| 11 | Mot de passe DB rotationné (l'ancien avait été exposé) | ⚠️ à confirmer |
| 12 | `LOG_LEVEL=error` (pas de fuite d'info en logs) | ✅ |

---

## 11. Maintenance courante

| Besoin | Commande |
|---|---|
| Modifier une variable du `.env` | `nano .env` puis `…php artisan config:clear` |
| Voir les erreurs applicatives | `tail -n 50 storage/logs/laravel.log` |
| Purger le cache Varnish | cPanel → *fastestcache* / *Purge Cache* |
| Après un `git pull` / réupload | `composer install --no-dev -o` → `config:clear` → `config:cache` → `route:cache` |
| Ajouter une table | créer la migration en local, puis `…php artisan migrate --force` en prod |
| Réinitialiser les caches | `config:clear && cache:clear && route:clear && view:clear` |

**Alias pratique** — ajouter dans `~/.bashrc` :

```bash
alias php83='/opt/alt/php83/usr/bin/php'
alias artisan='/opt/alt/php83/usr/bin/php /home/c2613905c/public_html/api-daoukro/artisan'
```

Puis `source ~/.bashrc`. Les alias ne fonctionnent **pas** dans la même ligne de commande que leur définition — il faut une nouvelle invite.

---

## 12. Chronologie des erreurs rencontrées

| Erreur | Cause réelle | Correction |
|---|---|---|
| `#1050 / #1062` à l'import SQL | dump non idempotent | `IF NOT EXISTS` + `ON DUPLICATE KEY UPDATE` + `FOREIGN_KEY_CHECKS=0` |
| `cd: No such file or directory` | home = `/home/c2613905**c**`, pas `/home/c2613905` | chemin exact |
| `Composer dependencies require PHP >= 8.3.0` | CLI en 8.2 | `/opt/alt/php83/usr/bin/php` |
| `/opt/cpanel/ea-php83/... No such file` | CageFS masque `/opt/cpanel` | binaires réels dans `/opt/alt/` |
| `SQLSTATE[1045] Access denied` | utilisateur MySQL non rattaché à la base + mauvais mot de passe | rattacher + ALL PRIVILEGES + rotation du mot de passe |
| `HTTP 403` sur `/` | DocumentRoot sur la racine du projet (pas d'`index.php`) | DocumentRoot → `/public` |
| `HTTP 500` sur `/public/` | PHP 8.2 exécutait du code Laravel 12 | `AddHandler` PHP 8.3 |
| Cookies sur `domain=.tondomaine.ci` | placeholder du `.env` non remplacé | `SESSION_DOMAIN=.akdev.tech` |
| `php83: command not found` | alias défini et utilisé sur la même ligne | chemin complet, ou `~/.bashrc` |

---

*Document généré pour AK Dev — projet Daoukro API.*

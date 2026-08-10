<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PharmacieController;
use App\Http\Controllers\Api\V1\ServicePublicController;
use App\Http\Controllers\Api\V1\CategorieServiceController;
use App\Http\Controllers\Api\V1\HebergementController;
use App\Http\Controllers\Api\V1\ImmobilierController;
use App\Http\Controllers\Api\V1\ArtisanController;
use App\Http\Controllers\Api\V1\AnnonceController;
use App\Http\Controllers\Api\V1\UrgenceController;
use App\Http\Controllers\Api\V1\ActualiteController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\AvisController;
use App\Http\Controllers\Api\V1\SignalementController;
use App\Http\Controllers\Api\V1\MesSoumissionsController;
use App\Http\Controllers\Api\V1\ProfilController;

// ═══════════════════════════════════════════════════════════════
// API v1 — Routes publiques
// ═══════════════════════════════════════════════════════════════
Route::prefix('v1')->group(function () {

    // ── Pharmacies ────────────────────────────────────────────
    Route::get('/pharmacies',               [PharmacieController::class, 'index']);
    Route::get('/pharmacies/garde/actives', [PharmacieController::class, 'gardesActives']);
    Route::get('/pharmacies/{id}',          [PharmacieController::class, 'show']);

    // ── Services publics ──────────────────────────────────────
    Route::get('/services-publics',                  [ServicePublicController::class, 'index']);
    Route::get('/services-publics/categorie/{id}',   [ServicePublicController::class, 'parCategorie']);
    Route::get('/services-publics/{id}',             [ServicePublicController::class, 'show']);

    // ── Catégories services ───────────────────────────────────
    Route::get('/categories-services', [CategorieServiceController::class, 'index']);

    // ── Hébergements ──────────────────────────────────────────
    Route::get('/hebergements',      [HebergementController::class, 'index']);
    Route::get('/hebergements/{id}', [HebergementController::class, 'show']);

    // ── Immobilier ────────────────────────────────────────────
    Route::get('/immobilier',      [ImmobilierController::class, 'index']);
    Route::get('/immobilier/{id}', [ImmobilierController::class, 'show']);

    // ── Artisans ──────────────────────────────────────────────
    Route::get('/artisans/metiers', [ArtisanController::class, 'metiers']);
    Route::get('/artisans',         [ArtisanController::class, 'index']);
    Route::get('/artisans/{id}',    [ArtisanController::class, 'show']);

    // ── Annonces ──────────────────────────────────────────────
    Route::get('/annonces',      [AnnonceController::class, 'index']);
    Route::get('/annonces/{id}', [AnnonceController::class, 'show']);

    // ── Urgences ──────────────────────────────────────────────
    Route::get('/urgences',      [UrgenceController::class, 'index']);
    Route::get('/urgences/{id}', [UrgenceController::class, 'show']);

    // ── Actualités ────────────────────────────────────────────
    Route::get('/actualites',      [ActualiteController::class, 'index']);
    Route::get('/actualites/{id}', [ActualiteController::class, 'show']);

    // ── Avis (artisans, hébergements, immobilier, annonces) ────────────────────
    Route::get('/{type}/{id}/avis', [AvisController::class, 'index'])
        ->where('type', 'artisan|hebergement|immobilier|annonce');
    Route::post('/{type}/{id}/avis', [AvisController::class, 'store'])
        ->where('type', 'artisan|hebergement|immobilier|annonce');

    // ── Signalements citoyens ───────────────────────────────────
    Route::post('/signalements', [SignalementController::class, 'store']);

    // ── Settings (contenus pilotables depuis l'admin) ─────────
    Route::get('/settings', [SettingController::class, 'index']);

    // ── Notifications FCM ─────────────────────────────────────
    Route::post('/fcm-token',           [NotificationController::class, 'storeToken']);
    Route::post('/notifications/envoyer', [NotificationController::class, 'envoyer']);

    // ── Authentification ──────────────────────────────────────
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login',    [AuthController::class, 'login']);
    Route::post('/auth/google',   [AuthController::class, 'google']);

});

// ═══════════════════════════════════════════════════════════════
// API v1 — Routes protégées (auth:sanctum)
// ═══════════════════════════════════════════════════════════════
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // ── Profil citoyen (PWA daoukro-pro) ─────────────────────────────────────
    Route::get('/profil', [ProfilController::class, 'show']);
    Route::put('/profil', [ProfilController::class, 'update']);

    // ── Auto-dépôt de fiches (PWA daoukro-pro) ───────────────────────────────

    // Artisans
    Route::get('/mes-soumissions/artisans',           [MesSoumissionsController::class, 'mesArtisans']);
    Route::post('/mes-soumissions/artisans',          [MesSoumissionsController::class, 'storeArtisan']);
    Route::get('/mes-soumissions/artisans/{id}',      [MesSoumissionsController::class, 'showArtisan']);
    Route::put('/mes-soumissions/artisans/{id}',      [MesSoumissionsController::class, 'updateArtisan']);
    Route::post('/mes-soumissions/artisans/{id}',     [MesSoumissionsController::class, 'updateArtisan']);
    Route::delete('/mes-soumissions/artisans/{id}',   [MesSoumissionsController::class, 'destroyArtisan']);

    // Hébergements
    Route::get('/mes-soumissions/hebergements',          [MesSoumissionsController::class, 'mesHebergements']);
    Route::post('/mes-soumissions/hebergements',         [MesSoumissionsController::class, 'storeHebergement']);
    Route::get('/mes-soumissions/hebergements/{id}',     [MesSoumissionsController::class, 'showHebergement']);
    Route::put('/mes-soumissions/hebergements/{id}',     [MesSoumissionsController::class, 'updateHebergement']);
    Route::post('/mes-soumissions/hebergements/{id}',    [MesSoumissionsController::class, 'updateHebergement']);
    Route::delete('/mes-soumissions/hebergements/{id}',  [MesSoumissionsController::class, 'destroyHebergement']);

    // Immobilier (double alias /immobilier et /immobiliers pour compatibilité PWA)
    Route::get('/mes-soumissions/immobilier',            [MesSoumissionsController::class, 'mesImmobiliers']);
    Route::get('/mes-soumissions/immobiliers',           [MesSoumissionsController::class, 'mesImmobiliers']);
    Route::post('/mes-soumissions/immobilier',           [MesSoumissionsController::class, 'storeImmobilier']);
    Route::post('/mes-soumissions/immobiliers',          [MesSoumissionsController::class, 'storeImmobilier']);
    Route::get('/mes-soumissions/immobilier/{id}',       [MesSoumissionsController::class, 'showImmobilier']);
    Route::get('/mes-soumissions/immobiliers/{id}',      [MesSoumissionsController::class, 'showImmobilier']);
    Route::put('/mes-soumissions/immobilier/{id}',       [MesSoumissionsController::class, 'updateImmobilier']);
    Route::post('/mes-soumissions/immobilier/{id}',      [MesSoumissionsController::class, 'updateImmobilier']);
    Route::post('/mes-soumissions/immobiliers/{id}',     [MesSoumissionsController::class, 'updateImmobilier']);
    Route::delete('/mes-soumissions/immobilier/{id}',    [MesSoumissionsController::class, 'destroyImmobilier']);
    Route::delete('/mes-soumissions/immobiliers/{id}',   [MesSoumissionsController::class, 'destroyImmobilier']);

    // Annonces
    Route::get('/mes-soumissions/annonces',          [MesSoumissionsController::class, 'mesAnnonces']);
    Route::post('/mes-soumissions/annonces',         [MesSoumissionsController::class, 'storeAnnonce']);
    Route::get('/mes-soumissions/annonces/{id}',     [MesSoumissionsController::class, 'showAnnonce']);
    Route::put('/mes-soumissions/annonces/{id}',     [MesSoumissionsController::class, 'updateAnnonce']);
    Route::post('/mes-soumissions/annonces/{id}',    [MesSoumissionsController::class, 'updateAnnonce']);
    Route::delete('/mes-soumissions/annonces/{id}',  [MesSoumissionsController::class, 'destroyAnnonce']);
});

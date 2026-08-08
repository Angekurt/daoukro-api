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

    // ── Avis (artisans, hébergements) ──────────────────────────
    Route::get('/{type}/{id}/avis', [AvisController::class, 'index'])
        ->where('type', 'artisan|hebergement');
    Route::post('/{type}/{id}/avis', [AvisController::class, 'store'])
        ->where('type', 'artisan|hebergement');

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
});

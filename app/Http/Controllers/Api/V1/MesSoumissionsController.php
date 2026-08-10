<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\Artisan;
use App\Models\Hebergement;
use App\Models\Immobilier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Auto-dépôt de fiches par les pros (artisans, hébergements...) depuis la
 * PWA daoukro-pro. Toute fiche créée ici est masquée du public
 * (`is_active = false`) jusqu'à validation par un admin/modérateur dans le
 * panel Filament — jamais publiée automatiquement.
 */
class MesSoumissionsController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Stocke jusqu'à 5 photos de galerie et retourne leur liste de chemins.
     * Les chemins déjà en base (strings) sont conservés tels quels.
     */
    private function stockerGalerie(Request $request, string $dossier, array $cheminsExistants = []): array
    {
        $chemins = $cheminsExistants;
        foreach ($request->file('photos', []) as $fichier) {
            if (count($chemins) >= 5) break;
            $chemins[] = $fichier->store($dossier . '/galerie', 'public');
        }
        return $chemins;
    }

    /** Renvoie 404 si la fiche n'appartient pas au citoyen connecté. */
    private function autoriser(mixed $fiche, Request $request): void
    {
        if (!$fiche || $fiche->citoyen_id !== $request->user()->id) {
            abort(404, 'Fiche introuvable.');
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ARTISANS
    // ══════════════════════════════════════════════════════════════════════════

    public function mesArtisans(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->artisans()->latest()->get(),
        ]);
    }

    public function showArtisan(Request $request, int $id): JsonResponse
    {
        $artisan = Artisan::find($id);
        $this->autoriser($artisan, $request);

        return response()->json(['success' => true, 'data' => $artisan]);
    }

    public function storeArtisan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:150',
            'metier'      => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'telephone'   => 'nullable|string|max:30',
            'whatsapp'    => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'adresse'     => 'nullable|string|max:255',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'photo'       => 'nullable|image|max:5120',
            'photos.*'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('artisans', 'public');
        }

        $data['photos']      = $this->stockerGalerie($request, 'artisans');
        $data['citoyen_id']  = $request->user()->id;
        $data['is_active']   = false;
        $data['disponible']  = true;

        $artisan = Artisan::create($data);

        return response()->json([
            'success' => true,
            'message' => "Fiche envoyée — elle sera visible dans l'app après validation par la mairie.",
            'data'    => $artisan,
        ], 201);
    }

    public function updateArtisan(Request $request, int $id): JsonResponse
    {
        $artisan = Artisan::find($id);
        $this->autoriser($artisan, $request);

        $data = $request->validate([
            'nom'         => 'sometimes|required|string|max:150',
            'metier'      => 'sometimes|required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'telephone'   => 'nullable|string|max:30',
            'whatsapp'    => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'adresse'     => 'nullable|string|max:255',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'photo'       => 'nullable|image|max:5120',
            'photos.*'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('artisans', 'public');
        }

        if ($request->hasFile('photos')) {
            $data['photos'] = $this->stockerGalerie($request, 'artisans', $artisan->photos ?? []);
        }

        // Modification remet en attente de validation
        $data['is_active'] = false;

        $artisan->update($data);

        return response()->json(['success' => true, 'data' => $artisan->fresh()]);
    }

    public function destroyArtisan(Request $request, int $id): JsonResponse
    {
        $artisan = Artisan::find($id);
        $this->autoriser($artisan, $request);
        $artisan->delete();

        return response()->json(['success' => true, 'message' => 'Fiche supprimée.']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // HÉBERGEMENTS
    // ══════════════════════════════════════════════════════════════════════════

    public function mesHebergements(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->hebergements()->latest()->get(),
        ]);
    }

    public function showHebergement(Request $request, int $id): JsonResponse
    {
        $h = Hebergement::find($id);
        $this->autoriser($h, $request);

        return response()->json(['success' => true, 'data' => $h]);
    }

    public function storeHebergement(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:150',
            'type'        => 'required|in:hotel,residence,meuble,auberge',
            'description' => 'nullable|string|max:1000',
            'adresse'     => 'nullable|string|max:255',
            'telephone'   => 'nullable|string|max:30',
            'whatsapp'    => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'prix_min'    => 'nullable|numeric',
            'prix_max'    => 'nullable|numeric',
            'photo'       => 'nullable|image|max:5120',
            'photos.*'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('hebergements', 'public');
        }

        $data['photos']     = $this->stockerGalerie($request, 'hebergements');
        $data['citoyen_id'] = $request->user()->id;
        $data['is_active']  = false;

        $h = Hebergement::create($data);

        return response()->json([
            'success' => true,
            'message' => "Fiche envoyée — elle sera visible dans l'app après validation par la mairie.",
            'data'    => $h,
        ], 201);
    }

    public function updateHebergement(Request $request, int $id): JsonResponse
    {
        $h = Hebergement::find($id);
        $this->autoriser($h, $request);

        $data = $request->validate([
            'nom'         => 'sometimes|required|string|max:150',
            'type'        => 'sometimes|required|in:hotel,residence,meuble,auberge',
            'description' => 'nullable|string|max:1000',
            'adresse'     => 'nullable|string|max:255',
            'telephone'   => 'nullable|string|max:30',
            'whatsapp'    => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'prix_min'    => 'nullable|numeric',
            'prix_max'    => 'nullable|numeric',
            'photo'       => 'nullable|image|max:5120',
            'photos.*'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('hebergements', 'public');
        }
        if ($request->hasFile('photos')) {
            $data['photos'] = $this->stockerGalerie($request, 'hebergements', $h->photos ?? []);
        }

        $data['is_active'] = false;
        $h->update($data);

        return response()->json(['success' => true, 'data' => $h->fresh()]);
    }

    public function destroyHebergement(Request $request, int $id): JsonResponse
    {
        $h = Hebergement::find($id);
        $this->autoriser($h, $request);
        $h->delete();

        return response()->json(['success' => true, 'message' => 'Fiche supprimée.']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // IMMOBILIER
    // ══════════════════════════════════════════════════════════════════════════

    public function mesImmobiliers(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->immobiliers()->latest()->get(),
        ]);
    }

    public function showImmobilier(Request $request, int $id): JsonResponse
    {
        $bien = Immobilier::find($id);
        $this->autoriser($bien, $request);

        return response()->json(['success' => true, 'data' => $bien]);
    }

    public function storeImmobilier(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titre'       => 'required|string|max:150',
            'type_offre'  => 'required|in:vente,location',
            'type_bien'   => 'required|in:maison,terrain,appartement,villa,bureau',
            'description' => 'nullable|string|max:1000',
            'adresse'     => 'nullable|string|max:255',
            'quartier'    => 'nullable|string|max:150',
            'prix'        => 'required|numeric',
            'surface'     => 'nullable|string|max:50',
            'nb_chambres' => 'nullable|integer',
            'telephone'   => 'nullable|string|max:30',
            'whatsapp'    => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'photo'       => 'nullable|image|max:5120',
            'photos.*'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('immobilier', 'public');
        }

        $data['photos']     = $this->stockerGalerie($request, 'immobilier');
        $data['citoyen_id'] = $request->user()->id;
        $data['is_active']  = false;

        $bien = Immobilier::create($data);

        return response()->json([
            'success' => true,
            'message' => "Fiche envoyée — elle sera visible dans l'app après validation par la mairie.",
            'data'    => $bien,
        ], 201);
    }

    public function updateImmobilier(Request $request, int $id): JsonResponse
    {
        $bien = Immobilier::find($id);
        $this->autoriser($bien, $request);

        $data = $request->validate([
            'titre'       => 'sometimes|required|string|max:150',
            'type_offre'  => 'sometimes|required|in:vente,location',
            'type_bien'   => 'sometimes|required|in:maison,terrain,appartement,villa,bureau',
            'description' => 'nullable|string|max:1000',
            'adresse'     => 'nullable|string|max:255',
            'quartier'    => 'nullable|string|max:150',
            'prix'        => 'sometimes|required|numeric',
            'surface'     => 'nullable|string|max:50',
            'nb_chambres' => 'nullable|integer',
            'telephone'   => 'nullable|string|max:30',
            'whatsapp'    => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'photo'       => 'nullable|image|max:5120',
            'photos.*'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('immobilier', 'public');
        }
        if ($request->hasFile('photos')) {
            $data['photos'] = $this->stockerGalerie($request, 'immobilier', $bien->photos ?? []);
        }

        $data['is_active'] = false;
        $bien->update($data);

        return response()->json(['success' => true, 'data' => $bien->fresh()]);
    }

    public function destroyImmobilier(Request $request, int $id): JsonResponse
    {
        $bien = Immobilier::find($id);
        $this->autoriser($bien, $request);
        $bien->delete();

        return response()->json(['success' => true, 'message' => 'Fiche supprimée.']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ANNONCES
    // ══════════════════════════════════════════════════════════════════════════

    public function mesAnnonces(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->annonces()->latest()->get(),
        ]);
    }

    public function showAnnonce(Request $request, int $id): JsonResponse
    {
        $annonce = Annonce::find($id);
        $this->autoriser($annonce, $request);

        return response()->json(['success' => true, 'data' => $annonce]);
    }

    public function storeAnnonce(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titre'       => 'required|string|max:150',
            'description' => 'required|string|max:1000',
            'type'        => 'required|in:evenement,emploi,restaurant,pub,annonce',
            'lieu'        => 'nullable|string|max:150',
            'date_debut'  => 'nullable|string|max:50',
            'date_fin'    => 'nullable|string|max:50',
            'contact'     => 'nullable|string|max:30',
            'telephone'   => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'lien'        => 'nullable|url|max:255',
            'photo'       => 'nullable|image|max:5120',
            'photos.*'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('annonces', 'public');
        }

        $data['photos']     = $this->stockerGalerie($request, 'annonces');
        $data['citoyen_id'] = $request->user()->id;
        $data['auteur']     = $request->user()->name;
        $data['is_active']  = false;

        $annonce = Annonce::create($data);

        return response()->json([
            'success' => true,
            'message' => "Annonce envoyée — elle sera visible dans l'app après validation par la mairie.",
            'data'    => $annonce,
        ], 201);
    }

    public function updateAnnonce(Request $request, int $id): JsonResponse
    {
        $annonce = Annonce::find($id);
        $this->autoriser($annonce, $request);

        $data = $request->validate([
            'titre'       => 'sometimes|required|string|max:150',
            'description' => 'sometimes|required|string|max:1000',
            'type'        => 'sometimes|required|in:evenement,emploi,restaurant,pub,annonce',
            'lieu'        => 'nullable|string|max:150',
            'date_debut'  => 'nullable|string|max:50',
            'date_fin'    => 'nullable|string|max:50',
            'contact'     => 'nullable|string|max:30',
            'telephone'   => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'lien'        => 'nullable|url|max:255',
            'photo'       => 'nullable|image|max:5120',
            'photos.*'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('annonces', 'public');
        }
        if ($request->hasFile('photos')) {
            $data['photos'] = $this->stockerGalerie($request, 'annonces', $annonce->photos ?? []);
        }

        $data['is_active'] = false;
        $annonce->update($data);

        return response()->json(['success' => true, 'data' => $annonce->fresh()]);
    }

    public function destroyAnnonce(Request $request, int $id): JsonResponse
    {
        $annonce = Annonce::find($id);
        $this->autoriser($annonce, $request);
        $annonce->delete();

        return response()->json(['success' => true, 'message' => 'Annonce supprimée.']);
    }
}

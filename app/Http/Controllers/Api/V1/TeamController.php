<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\InvitationEquipe;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\Citoyen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Gestion des équipes (multi-comptes) depuis la PWA daoukro-pro.
 *
 * Flux :
 *   Propriétaire crée une équipe → invite par email → le membre
 *   reçoit un email avec un lien contenant le token → accepte
 *   depuis la PWA → devient membre de l'équipe.
 */
class TeamController extends Controller
{
    // ── Mes équipes (propriétaire + membre) ───────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $citoyen = $request->user();

        $teams = Team::where('owner_id', $citoyen->id)
            ->orWhereHas('membres', fn ($q) => $q->where('citoyen_id', $citoyen->id))
            ->with(['owner', 'membres'])
            ->get()
            ->map(function (Team $team) use ($citoyen) {
                $estProprietaire = $team->owner_id === $citoyen->id;
                $role = $estProprietaire
                    ? 'owner'
                    : $team->membres->firstWhere('id', $citoyen->id)?->pivot->role ?? 'editor';

                return [
                    'id'               => $team->id,
                    'nom'              => $team->nom,
                    'description'      => $team->description,
                    'logo'             => $team->logo,
                    'role'             => $role,
                    'est_proprietaire' => $estProprietaire,
                    'nb_membres'       => $team->membres->count() + 1, // +1 pour le propriétaire
                ];
            });

        return response()->json(['success' => true, 'data' => $teams]);
    }

    // ── Détail d'une équipe ───────────────────────────────────────────────────

    public function show(Request $request, int $id): JsonResponse
    {
        $team = Team::with(['owner', 'membres', 'invitations'])->find($id);

        if (! $team || ! $team->aMembre($request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'Équipe introuvable.'], 404);
        }

        $estProprio = $team->owner_id === $request->user()->id;

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $team->id,
                'nom'              => $team->nom,
                'description'      => $team->description,
                'logo'             => $team->logo,
                'est_proprietaire' => $estProprio,
                'membres'          => $team->membres->map(fn ($m) => [
                    'id'         => $m->id,
                    'nom'        => $m->name,
                    'email'      => $m->email,
                    'avatar_url' => $m->avatar_url,
                    'role'       => $m->pivot->role,
                ])->prepend([
                    'id'         => $team->owner->id,
                    'nom'        => $team->owner->name,
                    'email'      => $team->owner->email,
                    'avatar_url' => $team->owner->avatar_url,
                    'role'       => 'owner',
                ]),
                'invitations_en_attente' => $estProprio
                    ? $team->invitations->where('expires_at', '>', now())->values()
                    : [],
            ],
        ]);
    }

    // ── Créer une équipe ──────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
        ]);

        $team = Team::create([
            'owner_id'    => $request->user()->id,
            'nom'         => $data['nom'],
            'description' => $data['description'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Équipe créée.',
            'data'    => $team,
        ], 201);
    }

    // ── Modifier une équipe ───────────────────────────────────────────────────

    public function update(Request $request, int $id): JsonResponse
    {
        $team = Team::find($id);

        if (! $team || $team->owner_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Équipe introuvable.'], 404);
        }

        $data = $request->validate([
            'nom'         => 'sometimes|required|string|max:150',
            'description' => 'nullable|string|max:500',
        ]);

        $team->update($data);

        return response()->json(['success' => true, 'data' => $team->fresh()]);
    }

    // ── Inviter un membre ─────────────────────────────────────────────────────

    public function inviter(Request $request, int $id): JsonResponse
    {
        $team = Team::find($id);

        if (! $team || $team->owner_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Équipe introuvable.'], 404);
        }

        $data = $request->validate([
            'email' => 'required|email|max:150',
            'role'  => 'required|in:manager,editor',
        ]);

        // Déjà membre ?
        $dejaMembreOuOwner = Citoyen::where('email', $data['email'])
            ->where(function ($q) use ($team) {
                $q->where('id', $team->owner_id)
                  ->orWhereHas('teamsMembre', fn ($q2) => $q2->where('team_id', $team->id));
            })->exists();

        if ($dejaMembreOuOwner) {
            return response()->json(['success' => false, 'message' => 'Cette personne est déjà membre de l\'équipe.'], 422);
        }

        // Supprimer l'ancienne invitation si elle existe
        TeamInvitation::where('team_id', $team->id)->where('email', $data['email'])->delete();

        $invitation = TeamInvitation::create([
            'team_id'    => $team->id,
            'email'      => $data['email'],
            'role'       => $data['role'],
            'token'      => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        // Envoyer l'email d'invitation
        try {
            Mail::to($data['email'])->send(new InvitationEquipe(
                nomEquipe:       $team->nom,
                nomInviteur:     $request->user()->name,
                role:            $data['role'],
                token:           $invitation->token,
                urlAcceptation:  config('app.url') . '/api/v1/teams/invitations/' . $invitation->token . '/accepter',
            ));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('TeamController: échec envoi invitation', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'message' => "Invitation envoyée à {$data['email']}.",
        ], 201);
    }

    // ── Accepter une invitation (via token dans l'email) ─────────────────────

    public function accepterInvitation(Request $request, string $token): JsonResponse
    {
        $invitation = TeamInvitation::where('token', $token)->first();

        if (! $invitation || $invitation->estExpire()) {
            return response()->json(['success' => false, 'message' => 'Invitation invalide ou expirée.'], 404);
        }

        $citoyen = $request->user();

        // Vérifier que l'email correspond
        if ($citoyen->email !== $invitation->email) {
            return response()->json([
                'success' => false,
                'message' => "Cette invitation est destinée à {$invitation->email}.",
            ], 403);
        }

        // Ajouter comme membre
        $invitation->team->membres()->syncWithoutDetaching([
            $citoyen->id => ['role' => $invitation->role],
        ]);

        $invitation->delete();

        return response()->json([
            'success' => true,
            'message' => "Vous avez rejoint l'équipe « {$invitation->team->nom} ».",
            'data'    => [
                'team_id' => $invitation->team_id,
                'role'    => $invitation->role,
            ],
        ]);
    }

    // ── Retirer un membre ─────────────────────────────────────────────────────

    public function retirerMembre(Request $request, int $teamId, int $citoyenId): JsonResponse
    {
        $team = Team::find($teamId);

        if (! $team || $team->owner_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Équipe introuvable.'], 404);
        }

        if ($citoyenId === $team->owner_id) {
            return response()->json(['success' => false, 'message' => 'Impossible de retirer le propriétaire.'], 422);
        }

        $team->membres()->detach($citoyenId);

        return response()->json(['success' => true, 'message' => 'Membre retiré.']);
    }

    // ── Quitter une équipe (membre) ───────────────────────────────────────────

    public function quitter(Request $request, int $id): JsonResponse
    {
        $team = Team::find($id);

        if (! $team) {
            return response()->json(['success' => false, 'message' => 'Équipe introuvable.'], 404);
        }

        if ($team->owner_id === $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Le propriétaire ne peut pas quitter son équipe. Supprimez-la.'], 422);
        }

        $team->membres()->detach($request->user()->id);

        return response()->json(['success' => true, 'message' => 'Vous avez quitté l\'équipe.']);
    }

    // ── Supprimer une équipe ──────────────────────────────────────────────────

    public function destroy(Request $request, int $id): JsonResponse
    {
        $team = Team::find($id);

        if (! $team || $team->owner_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Équipe introuvable.'], 404);
        }

        $team->delete();

        return response()->json(['success' => true, 'message' => 'Équipe supprimée.']);
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Page Filament de gestion des tarifs d'abonnement.
 * Modifie directement les entrées JSON dans la table `settings` (groupe = plans).
 * Les nouveaux prix sont immédiatement pris en compte dans la PWA daoukro-pro
 * et dans les widgets financiers du dashboard, sans redéploiement.
 *
 * Réservée aux admins.
 */
class GererTarifs extends Page
{
    protected string $view = 'filament.pages.gerer-tarifs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Tarifs & Abonnements';

    protected static ?string $title = 'Gestion des tarifs';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        // Charger les valeurs actuelles depuis les Settings
        $valeurs = [];
        foreach (['free', 'standard', 'pro', 'business'] as $plan) {
            $setting = Setting::where('cle', "plan_{$plan}")->first();
            if ($setting) {
                $json = json_decode($setting->valeur, true) ?? [];
                $valeurs["plan_{$plan}_prix"]          = $json['prix_fcfa'] ?? 0;
                $valeurs["plan_{$plan}_quota_fiches"]  = $json['quota_fiches'] ?? 1;
                $valeurs["plan_{$plan}_quota_membres"] = $json['quota_membres'] ?? 0;
                $valeurs["plan_{$plan}_push_mensuel"]  = $json['push_mensuel'] ?? 0;
                $valeurs["plan_{$plan}_badge"]         = $json['badge_verifie'] ?? false;
                $valeurs["plan_{$plan}_stats"]         = $json['stats_avancees'] ?? false;
                $valeurs["plan_{$plan}_avant"]         = $json['mise_en_avant'] ?? false;
                $valeurs["plan_{$plan}_nom"]           = $json['nom'] ?? ucfirst($plan);
                $valeurs["plan_{$plan}_description"]   = $json['description'] ?? '';
            }
        }
        $this->form->fill($valeurs);
    }

    public function form(Schema $schema): Schema
    {
        $plans = [
            ['id' => 'free',     'label' => 'Plan Gratuit',  'couleur' => 'gray'],
            ['id' => 'standard', 'label' => 'Plan Standard', 'couleur' => 'info'],
            ['id' => 'pro',      'label' => 'Plan Pro',      'couleur' => 'warning'],
            ['id' => 'business', 'label' => 'Plan Business', 'couleur' => 'success'],
        ];

        $sections = [];
        foreach ($plans as $plan) {
            $id = $plan['id'];
            $sections[] = Section::make($plan['label'])
                ->description("Configurez les tarifs et quotas du plan {$plan['label']}. Les modifications sont appliquées immédiatement dans la PWA.")
                ->collapsible()
                ->collapsed($id === 'free') // Gratuit replié par défaut
                ->columns(2)
                ->schema([
                    TextInput::make("plan_{$id}_nom")
                        ->label('Nom affiché')
                        ->required(),
                    TextInput::make("plan_{$id}_prix")
                        ->label('Prix mensuel (F CFA)')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('F CFA / mois')
                        ->required()
                        ->helperText($id === 'free' ? 'Mettre 0 pour le plan gratuit' : ''),
                    Textarea::make("plan_{$id}_description")
                        ->label('Description courte')
                        ->rows(2)
                        ->columnSpanFull(),
                    TextInput::make("plan_{$id}_quota_fiches")
                        ->label('Fiches actives max')
                        ->numeric()
                        ->helperText('-1 = illimité')
                        ->required(),
                    TextInput::make("plan_{$id}_quota_membres")
                        ->label('Membres équipe max')
                        ->numeric()
                        ->helperText('0 = pas d\'équipe, -1 = illimité')
                        ->required(),
                    TextInput::make("plan_{$id}_push_mensuel")
                        ->label('Notifications push / mois')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                    Toggle::make("plan_{$id}_badge")
                        ->label('Badge vérifié'),
                    Toggle::make("plan_{$id}_stats")
                        ->label('Statistiques avancées'),
                    Toggle::make("plan_{$id}_avant")
                        ->label('Mise en avant dans l\'app'),
                ]);
        }

        return $schema
            ->components($sections)
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('enregistrer')
                ->label('Enregistrer tous les tarifs')
                ->color('primary')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->requiresConfirmation()
                ->modalHeading('Confirmer la mise à jour des tarifs')
                ->modalDescription('Les nouveaux tarifs seront immédiatement visibles dans la PWA daoukro-pro et dans les rapports financiers.')
                ->action('sauvegarder'),
        ];
    }

    public function sauvegarder(): void
    {
        $state = $this->form->getState();
        $modifies = 0;

        foreach (['free', 'standard', 'pro', 'business'] as $plan) {
            $setting = Setting::where('cle', "plan_{$plan}")->first();
            if (!$setting) continue;

            $ancienJson = json_decode($setting->valeur, true) ?? [];

            $nouveauJson = array_merge($ancienJson, [
                'id'             => $plan,
                'nom'            => $state["plan_{$plan}_nom"] ?? $ancienJson['nom'] ?? ucfirst($plan),
                'description'    => $state["plan_{$plan}_description"] ?? '',
                'prix_fcfa'      => (int) ($state["plan_{$plan}_prix"] ?? 0),
                'quota_fiches'   => (int) ($state["plan_{$plan}_quota_fiches"] ?? 1),
                'quota_membres'  => (int) ($state["plan_{$plan}_quota_membres"] ?? 0),
                'push_mensuel'   => (int) ($state["plan_{$plan}_push_mensuel"] ?? 0),
                'badge_verifie'  => (bool) ($state["plan_{$plan}_badge"] ?? false),
                'stats_avancees' => (bool) ($state["plan_{$plan}_stats"] ?? false),
                'mise_en_avant'  => (bool) ($state["plan_{$plan}_avant"] ?? false),
                // Conserver les champs non éditables ici
                'duree_jours'    => $ancienJson['duree_jours'] ?? 30,
                'quota_photos'   => $ancienJson['quota_photos'] ?? 5,
            ]);

            $setting->update(['valeur' => json_encode($nouveauJson, JSON_UNESCAPED_UNICODE)]);
            $modifies++;
        }

        Notification::make()
            ->title("{$modifies} plan(s) mis à jour avec succès")
            ->body('Les nouveaux tarifs sont actifs immédiatement dans la PWA et les rapports.')
            ->success()
            ->send();
    }
}

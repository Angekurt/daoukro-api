<?php

namespace App\Filament\Pages;

use App\Models\FcmToken;
use App\Services\FcmService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EnvoyerNotification extends Page
{
    protected string $view = 'filament.pages.envoyer-notification';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static ?string $navigationLabel = 'Envoyer une notification';

    protected static ?string $title = 'Envoyer une notification';

    // Réservé aux comptes admin — l'envoi touche tous les téléphones à la
    // fois, un modérateur ne doit pas pouvoir déclencher ça par erreur.
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
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titre')
                    ->label('Titre')
                    ->required()
                    ->maxLength(100),

                Textarea::make('corps')
                    ->label('Message')
                    ->required()
                    ->maxLength(500)
                    ->rows(4),

                Select::make('type')
                    ->label('Type')
                    ->options([
                        'info' => 'Information',
                        'alerte' => 'Alerte',
                        'sante' => 'Santé',
                        'mairie' => 'Mairie',
                        'pharmacie' => 'Pharmacie',
                    ])
                    ->default('info')
                    ->required(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('envoyer')
                ->label('Envoyer à tous les appareils')
                ->color('primary')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->requiresConfirmation()
                ->modalDescription('La notification sera envoyée immédiatement à tous les téléphones ayant l\'application installée.')
                ->action('envoyer'),
        ];
    }

    public function envoyer(): void
    {
        $state = $this->form->getState();

        $deviceTokens = \App\Models\AppDevice::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->pluck('fcm_token')
            ->toArray();

        $legacyTokens = FcmToken::pluck('token')->toArray();

        $tokens = array_values(array_unique(array_filter(array_merge($deviceTokens, $legacyTokens))));

        if (empty($tokens)) {
            Notification::make()
                ->title('Aucun appareil enregistré')
                ->warning()
                ->send();
            return;
        }

        $fcm = app(FcmService::class);

        if (! $fcm->estConfigure()) {
            Notification::make()
                ->title('FCM non configuré')
                ->body('Le fichier de compte de service Firebase est introuvable côté serveur.')
                ->danger()
                ->send();
            return;
        }

        $envoyes = $fcm->envoyerA($tokens, $state['titre'], $state['corps'], [
            'type' => $state['type'],
            'titre' => $state['titre'],
            'corps' => $state['corps'],
        ]);

        Notification::make()
            ->title("Notification envoyée à $envoyes / " . count($tokens) . ' appareil(s)')
            ->success()
            ->send();

        $this->form->fill();
    }
}

<?php

namespace App\Filament\Resources\Citoyens;

use App\Filament\Resources\Citoyens\Pages\ListCitoyens;
use App\Filament\Resources\Citoyens\Pages\EditCitoyen;
use App\Models\Citoyen;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CitoyenResource extends Resource
{
    protected static ?string $model = Citoyen::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static ?string $navigationLabel = 'Comptes Pros';
    protected static ?string $navigationGroup = 'Utilisateurs';
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('statut', 'actif')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nom')->required(),
            TextInput::make('prenom')->label('Prénom')->nullable(),
            TextInput::make('email')->email()->nullable(),
            TextInput::make('telephone')->nullable(),
            TextInput::make('plan')->label('Plan actif')->disabled(),
            Select::make('statut')
                ->label('Statut du compte')
                ->options([
                    'actif'    => 'Actif',
                    'suspendu' => 'Suspendu',
                    'en_veille' => 'En veille',
                ])
                ->required(),
            Toggle::make('est_actif')->label('Compte activé')->default(true),
            Textarea::make('note_admin')
                ->label('Note interne (motif de suspension, historique...)')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')->label('')->circular()->defaultImageUrl(fn ($record) => null),
                TextColumn::make('name')->label('Nom')->searchable()->weight('bold')
                    ->description(fn ($record) => $record->prenom),
                TextColumn::make('email')->searchable()->placeholder('—'),
                TextColumn::make('telephone')->placeholder('—'),
                TextColumn::make('auth_provider')->label('Auth')->badge()
                    ->formatStateUsing(fn ($state) => $state === 'google' ? 'Google' : 'Email'),
                TextColumn::make('plan')->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'free'     => 'Gratuit',
                        'standard' => 'Standard',
                        'pro'      => 'Pro',
                        'business' => 'Business',
                        default    => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'standard' => 'info',
                        'pro'      => 'warning',
                        'business' => 'success',
                        default    => 'gray',
                    }),
                TextColumn::make('statut')->badge()
                    ->color(fn ($state) => match($state) {
                        'actif'    => 'success',
                        'suspendu' => 'danger',
                        'en_veille' => 'warning',
                        default    => 'gray',
                    }),
                TextColumn::make('plan_expire_at')->label('Expiration plan')
                    ->date('d/m/Y')->placeholder('—')->sortable(),
                TextColumn::make('created_at')->label('Inscrit le')->date('d/m/Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('statut')->options([
                    'actif'     => 'Actif',
                    'suspendu'  => 'Suspendu',
                    'en_veille' => 'En veille',
                ]),
                SelectFilter::make('plan')->options([
                    'free'     => 'Gratuit',
                    'standard' => 'Standard',
                    'pro'      => 'Pro',
                    'business' => 'Business',
                ]),
                SelectFilter::make('auth_provider')->label('Authentification')->options([
                    'google' => 'Google',
                    'email'  => 'Email',
                ]),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                Action::make('suspendre')
                    ->label('Suspendre')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn ($record) => $record->statut === 'actif')
                    ->form([
                        Textarea::make('note_admin')
                            ->label('Motif de la suspension')
                            ->required()->maxLength(500)->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'statut'     => 'suspendu',
                            'est_actif'  => false,
                            'note_admin' => $data['note_admin'],
                        ]);
                        Notification::make()->title('Compte suspendu')->warning()->send();
                    }),
                Action::make('reactiver')
                    ->label('Réactiver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->statut !== 'actif')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['statut' => 'actif', 'est_actif' => true, 'note_admin' => null]);
                        Notification::make()->title('Compte réactivé')->success()->send();
                    }),
                Action::make('mettre_veille')
                    ->label('Mettre en veille')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->visible(fn ($record) => $record->statut === 'actif')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['statut' => 'en_veille'])),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCitoyens::route('/'),
            'edit'  => EditCitoyen::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\AppDevices;

use App\Filament\Resources\AppDevices\Pages\ListAppDevices;
use App\Models\AppDevice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AppDeviceResource extends Resource
{
    protected static ?string $model = AppDevice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDevicePhoneMobile;

    protected static ?string $navigationLabel = 'Appareils & PWA';

    protected static ?string $navigationGroup = 'Administration';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count > 0 ? (string) $count : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('device_id')
                    ->label('Identifiant Unique')
                    ->searchable()
                    ->copyable()
                    ->limit(20)
                    ->description(fn (AppDevice $record) => $record->device_model ?? 'Modèle inconnu'),

                TextColumn::make('platform')
                    ->label('Plateforme')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'android' => 'Android (APK)',
                        'ios_pwa' => 'iOS (PWA)',
                        'ios_app' => 'iOS (Natif)',
                        'web'     => 'Web Navigateur',
                        default   => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'android' => 'success',
                        'ios_pwa' => 'info',
                        'ios_app' => 'primary',
                        default   => 'gray',
                    }),

                TextColumn::make('os_version')
                    ->label('Système / OS')
                    ->placeholder('—'),

                TextColumn::make('app_version')
                    ->label('Version App')
                    ->placeholder('1.0.0'),

                IconColumn::make('has_push')
                    ->label('Push Activé')
                    ->boolean()
                    ->state(fn (AppDevice $record) => ! empty($record->fcm_token)),

                TextColumn::make('last_active_at')
                    ->label('Dernière Activité')
                    ->since()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Installé le')
                    ->date('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('last_active_at', 'desc')
            ->filters([
                SelectFilter::make('platform')
                    ->label('Plateforme')
                    ->options([
                        'android' => 'Android (APK)',
                        'ios_pwa' => 'iOS (PWA)',
                        'web'     => 'Web',
                    ]),
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
            'index' => ListAppDevices::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Gardes\Schemas;

use App\Models\Pharmacie;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GardeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pharmacie_id')
                    ->label('Pharmacie')
                    ->options(Pharmacie::where('is_active', true)->pluck('nom', 'id'))
                    ->required()
                    ->searchable(),

                DatePicker::make('date_debut')
                    ->label('Date début de garde')
                    ->required()
                    ->native(false),

                DatePicker::make('date_fin')
                    ->label('Date fin de garde')
                    ->required()
                    ->native(false),

                Textarea::make('note')
                    ->label('Note')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }
}

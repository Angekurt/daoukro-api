<?php

namespace App\Filament\Resources\Actualites\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ActualiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titre')
                    ->required()
                    ->maxLength(200)
                    ->columnSpanFull(),

                Select::make('categorie')
                    ->options([
                        'alerte' => 'Alerte',
                        'mairie' => 'Mairie',
                        'sante' => 'Santé',
                        'info' => 'Info',
                    ])
                    ->default('info')
                    ->required(),

                RichEditor::make('contenu')
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('photo')
                    ->label('Photo de couverture')
                    ->image()
                    ->directory('actualites')
                    ->columnSpanFull(),

                FileUpload::make('photos')
                    ->label('Galerie')
                    ->helperText('Jusqu\'à 4 photos supplémentaires affichées dans l\'article.')
                    ->image()
                    ->multiple()
                    ->maxFiles(4)
                    ->reorderable()
                    ->directory('actualites/galerie')
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Publiée')
                    ->default(true),
            ]);
    }
}

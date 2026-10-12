<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations du Projet')
                    ->description('Détails principaux de la réalisation')
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Caractéristiques & Liens')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('category')
                                ->label('Catégorie (ex: Web, Mobile, Design)')
                                ->required(),
                            TextInput::make('stack')
                                ->label('Stack Technique (ex: Laravel, Vue, Tailwind)'),
                        ]),
                        TextInput::make('link')
                            ->label('Lien vers le projet (URL)')
                            ->url()
                            ->columnSpanFull(),
                    ]),

                Section::make('Visuels')
                    ->description('Images et présentation visuelle')
                    ->schema([
                        FileUpload::make('image_url')
                            ->label('Image principale')
                            ->image()
                            ->directory('projects')
                            ->columnSpanFull(),
                        TextInput::make('gradient_classes')
                            ->label('Classes CSS pour le dégradé (ex: from-blue-500 to-purple-600)')
                            ->helperText('Optionnel : pour styliser la carte sur le site public.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}

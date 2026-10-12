<?php

namespace App\Filament\Resources\QuoteRequests\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations du demandeur')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')->label('Nom complet')->required(),
                            TextInput::make('email')->label('Email')->email()->required(),
                            TextInput::make('phone')->label('Téléphone'),
                            TextInput::make('company')->label('Entreprise'),
                        ]),
                    ]),

                Section::make('Détails du projet')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('project_type')
                                ->label('Type de projet')
                                ->options([
                                    'site-web' => 'Site Web',
                                    'application' => 'Application Mobile/Web',
                                    'design' => 'Design & Branding',
                                    'ia' => 'Intelligence Artificielle',
                                    'conseil' => 'Conseil / Audit',
                                    'autre' => 'Autre',
                                ]),
                            Select::make('budget')
                                ->label('Budget estimé')
                                ->options([
                                    '< 500€' => 'Moins de 500€',
                                    '500-2000€' => '500€ – 2 000€',
                                    '2000-5000€' => '2 000€ – 5 000€',
                                    '5000-15000€' => '5 000€ – 15 000€',
                                    '> 15000€' => 'Plus de 15 000€',
                                    'non-defini' => 'Non défini pour l\'instant',
                                ]),
                        ]),
                        Textarea::make('description')
                            ->label('Description du projet')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Statut')
                    ->schema([
                        Select::make('status')
                            ->label('Statut')
                            ->options(['pending' => 'En attente', 'replied' => 'Répondu'])
                            ->default('pending'),
                        Textarea::make('admin_reply')
                            ->label('Réponse envoyée')
                            ->rows(4)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}

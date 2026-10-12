<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('👤 Informations du client')
                    ->description('Coordonnées de la personne qui a pris rendez-vous')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom complet')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('company')
                            ->label('Entreprise')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('📅 Date & Heure')
                    ->description('Planification du rendez-vous')
                    ->schema([
                        Select::make('type')
                            ->label('Type de rendez-vous')
                            ->options([
                                'gratuite' => 'Consultation gratuite',
                                'audit' => 'Audit',
                                'formation' => 'Formation',
                                'dev' => 'Développement',
                                'ia' => 'Accompagnement IA',
                            ])
                            ->required()
                            ->columnSpan(1),
                        Select::make('contact_channel')
                            ->label('Canal de contact')
                            ->options([
                                'tel' => 'Téléphone',
                                'meet' => 'Google Meet',
                                'wa' => 'WhatsApp',
                            ])
                            ->required()
                            ->columnSpan(1),
                        DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),
                        TimePicker::make('time')
                            ->label('Heure')
                            ->required()
                            ->seconds(false)
                            ->columnSpan(1),
                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'validated' => 'Validé',
                                'cancelled' => 'Annulé',
                            ])
                            ->default('pending')
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Section::make('📝 Message')
                    ->description('Message laissé par le client lors de la prise de rendez-vous')
                    ->schema([
                        Textarea::make('message')
                            ->label('Message')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}

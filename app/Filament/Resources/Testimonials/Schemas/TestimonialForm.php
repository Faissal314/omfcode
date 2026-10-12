<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('client_name')
                    ->label('Nom du client')
                    ->required(),
                TextInput::make('client_role')
                    ->label('Rôle / Entreprise'),
                TextInput::make('initials')
                    ->label('Initiales')
                    ->required(),
                Textarea::make('content')
                    ->label('Contenu du témoignage')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('rating')
                    ->label('Note (étoiles)')
                    ->required()
                    ->numeric()
                    ->default(5),
                Toggle::make('is_approved')
                    ->label('Approuvé / Publié')
                    ->default(true),
            ]);
    }
}

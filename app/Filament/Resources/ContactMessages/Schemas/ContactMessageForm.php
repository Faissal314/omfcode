<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->disabled(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->disabled(),
                TextInput::make('phone')
                    ->label('Téléphone')
                    ->tel()
                    ->disabled(),
                TextInput::make('subject')
                    ->label('Sujet')
                    ->required()
                    ->disabled()
                    ->columnSpanFull(),
                Textarea::make('message')
                    ->label('Message')
                    ->required()
                    ->rows(6)
                    ->disabled()
                    ->columnSpanFull(),
                Toggle::make('is_read')
                    ->label('Marqué comme lu')
                    ->onColor('success'),
            ]);
    }
}

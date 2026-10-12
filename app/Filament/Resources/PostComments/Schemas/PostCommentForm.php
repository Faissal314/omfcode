<?php

namespace App\Filament\Resources\PostComments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PostCommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('post_id')
                    ->relationship('post', 'title')
                    ->label('Article')
                    ->required()
                    ->disabled(),
                TextInput::make('name')
                    ->label('Auteur')
                    ->required()
                    ->disabled(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->disabled(),
                Textarea::make('content')
                    ->label('Commentaire')
                    ->required()
                    ->disabled()
                    ->columnSpanFull(),
                Toggle::make('is_approved')
                    ->label('Approuvé / Visible publiquement')
                    ->required(),
            ]);
    }
}

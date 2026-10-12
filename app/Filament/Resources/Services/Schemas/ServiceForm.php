<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label('Catégorie'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('short_description')
                    ->required(),
                RichEditor::make('content')
                    ->label('Contenu détaillé')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}

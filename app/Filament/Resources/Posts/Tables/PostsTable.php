<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Image')
                    ->disk('public')
                    ->square(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold'),

                TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->searchable(),

                IconColumn::make('is_featured')
                    ->label('À la une')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                TextColumn::make('views')
                    ->label('Vues')
                    ->icon('heroicon-o-eye')
                    ->sortable()
                    ->default(0),

                TextColumn::make('likes')
                    ->label('Likes')
                    ->icon('heroicon-o-heart')
                    ->sortable()
                    ->default(0),

                TextColumn::make('created_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_featured')
                    ->label('Mise en avant')
                    ->trueLabel('À la une')
                    ->falseLabel('Standard'),
            ])
            ->recordActions([
                EditAction::make()->label('Modifier'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Supprimer'),
                ]),
            ]);
    }
}

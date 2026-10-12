<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Image')
                    ->square(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->searchable(),

                TextColumn::make('stack')
                    ->label('Stack Technique')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('link')
                    ->label('Lien')
                    ->url(fn ($record) => $record->link)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->limit(20)
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options(fn () => Project::query()->distinct()->pluck('category', 'category')->toArray()),
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

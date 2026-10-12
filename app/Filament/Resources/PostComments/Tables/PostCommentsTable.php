<?php

namespace App\Filament\Resources\PostComments\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PostCommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('is_approved', 'asc') // Tri par défaut : non approuvés en premier
            ->columns([
                IconColumn::make('is_approved')
                    ->label('Statut')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Auteur')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->email),

                TextColumn::make('post.title')
                    ->label('Article')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->color('primary'),

                TextColumn::make('content')
                    ->label('Commentaire')
                    ->limit(50)
                    ->wrap()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Écrit le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_approved')
                    ->label('Statut d\'approbation')
                    ->trueLabel('Approuvés')
                    ->falseLabel('En attente'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->is_approved)
                    ->action(fn ($record) => $record->update(['is_approved' => true])),

                Action::make('reject')
                    ->label('Masquer')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->is_approved)
                    ->action(fn ($record) => $record->update(['is_approved' => false])),

                EditAction::make()->label('Modifier'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Supprimer'),
                ]),
            ]);
    }
}

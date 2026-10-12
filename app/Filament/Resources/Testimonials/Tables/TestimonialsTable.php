<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->client_role),

                TextColumn::make('initials')
                    ->label('Init.')
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('rating')
                    ->label('Note')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),

                TextColumn::make('content')
                    ->label('Témoignage')
                    ->limit(50)
                    ->wrap()
                    ->color('gray'),

                IconColumn::make('is_approved')
                    ->label('Statut')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
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

<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Mail\AppointmentCancelledMail;
use App\Mail\AppointmentValidatedMail;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'asc')
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->date >= now()->toDateString() ? 'success' : 'gray'),

                TextColumn::make('time')
                    ->label('Heure')
                    ->time('H\hi')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'gratuite' => 'info',
                        'audit' => 'warning',
                        'formation' => 'success',
                        'dev' => 'primary',
                        'ia' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('contact_channel')
                    ->label('Canal')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'wa' => 'success',
                        'meet' => 'info',
                        'tel' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'wa' => 'WhatsApp',
                        'meet' => 'Google Meet',
                        'tel' => 'Téléphone',
                        default => $state,
                    }),

                TextColumn::make('name')
                    ->label('Client')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copié !'),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'validated' => 'success',
                        'cancelled' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'validated' => 'Validé',
                        'cancelled' => 'Annulé',
                        default => 'En attente',
                    }),
            ])
            ->filters([
                Filter::make('upcoming')
                    ->label('À venir')
                    ->query(fn (Builder $query) => $query->where('date', '>=', now()->toDateString())),

                Filter::make('past')
                    ->label('Passés')
                    ->query(fn (Builder $query) => $query->where('date', '<', now()->toDateString())),

                SelectFilter::make('type')
                    ->label('Type de RDV')
                    ->options([
                        'gratuite' => 'Consultation gratuite',
                        'audit' => 'Audit',
                        'formation' => 'Formation',
                        'dev' => 'Développement',
                        'ia' => 'Accompagnement IA',
                    ]),

                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'validated' => 'Validé',
                        'cancelled' => 'Annulé',
                    ]),
            ])
            ->recordActions([
                Action::make('validate')
                    ->label('Valider')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Valider le rendez-vous')
                    ->modalDescription('Un email de confirmation sera envoyé au client.')
                    ->action(function ($record) {
                        $record->update(['status' => 'validated']);
                        Mail::to($record->email)->send(new AppointmentValidatedMail($record));
                        Notification::make()->title('Rendez-vous validé et email envoyé.')->success()->send();
                    }),

                Action::make('cancel')
                    ->label('Annuler')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Annuler le rendez-vous')
                    ->modalDescription('Un email d\'annulation sera envoyé au client.')
                    ->action(function ($record) {
                        $record->update(['status' => 'cancelled']);
                        Mail::to($record->email)->send(new AppointmentCancelledMail($record));
                        Notification::make()->title('Rendez-vous annulé et email envoyé.')->success()->send();
                    }),

                ViewAction::make()->label('Voir'),
                EditAction::make()->label('Modifier'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Supprimer'),
                ]),
            ]);
    }
}

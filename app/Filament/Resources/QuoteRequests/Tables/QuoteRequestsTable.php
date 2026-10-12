<?php

namespace App\Filament\Resources\QuoteRequests\Tables;

use App\Mail\QuoteResponseMail;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class QuoteRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'replied' ? 'success' : 'warning')
                    ->formatStateUsing(fn (string $state): string => $state === 'replied' ? 'Répondu' : 'En attente')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Client')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->company),

                TextColumn::make('project_type')
                    ->label('Type de projet')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('budget')
                    ->label('Budget')
                    ->placeholder('Non précisé'),

                TextColumn::make('email')
                    ->label('Email')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->wrap()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Reçu le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'replied' => 'Répondu',
                    ]),
            ])
            ->recordActions([
                Action::make('reply')
                    ->label('Répondre')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        Textarea::make('reply_text')
                            ->label('Votre réponse / devis')
                            ->required()
                            ->rows(8)
                            ->placeholder('Saisissez votre devis ou réponse ici...'),
                    ])
                    ->action(function ($record, array $data) {
                        Mail::to($record->email)->send(new QuoteResponseMail($record, $data['reply_text']));
                        $record->update([
                            'status' => 'replied',
                            'admin_reply' => $data['reply_text'],
                        ]);
                        Notification::make()->title('Devis envoyé par email !')->success()->send();
                    }),

                ViewAction::make()->label('Voir'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Supprimer'),
                ]),
            ]);
    }
}

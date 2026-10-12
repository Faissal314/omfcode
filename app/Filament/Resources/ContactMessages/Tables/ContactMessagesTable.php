<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Mail\ContactMessageReplyMail;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('is_read')
                    ->label('Lu')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('name')
                    ->label('Expéditeur')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('subject')
                    ->label('Sujet')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('message')
                    ->label('Aperçu')
                    ->limit(60)
                    ->wrap()
                    ->color('gray'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copié !')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Reçu le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                TernaryFilter::make('is_read')
                    ->label('Statut')
                    ->trueLabel('Lus')
                    ->falseLabel('Non lus'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lire')
                    ->icon('heroicon-o-eye'),

                Action::make('reply')
                    ->label('Répondre')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->form([
                        Textarea::make('reply_text')
                            ->label('Votre réponse')
                            ->required()
                            ->rows(6)
                            ->placeholder('Saisissez votre réponse ici...'),
                    ])
                    ->action(function ($record, array $data) {
                        Mail::to($record->email)->send(new ContactMessageReplyMail(
                            clientName: $record->name,
                            originalSubject: $record->subject ?? 'votre message',
                            replyText: $data['reply_text']
                        ));
                        $record->update(['is_read' => true]);
                        Notification::make()->title('Réponse envoyée par email !')->success()->send();
                    }),

                Action::make('mark_read')
                    ->label('')
                    ->tooltip('Marquer comme lu')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->is_read)
                    ->action(fn ($record) => $record->update(['is_read' => true])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_all_read')
                        ->label('Marquer comme lus')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_read' => true]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make()->label('Supprimer'),
                ]),
            ]);
    }
}

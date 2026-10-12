<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Mail\AppointmentCancelledMail;
use App\Mail\AppointmentValidatedMail;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

class ViewAppointment extends ViewRecord
{
    protected static string $resource = AppointmentResource::class;

    public function getHeaderActions(): array
    {
        return [
            Action::make('validate')
                ->label('Valider le RDV')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->modalHeading('Valider ce rendez-vous ?')
                ->modalDescription('Un email de confirmation sera envoyé au client.')
                ->action(function () {
                    $this->record->update(['status' => 'validated']);
                    Mail::to($this->record->email)->send(new AppointmentValidatedMail($this->record));
                    Notification::make()->title('Rendez-vous validé ! Email envoyé.')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('cancel')
                ->label('Annuler le RDV')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->modalHeading('Annuler ce rendez-vous ?')
                ->modalDescription('Un email d\'annulation sera envoyé au client.')
                ->action(function () {
                    $this->record->update(['status' => 'cancelled']);
                    Mail::to($this->record->email)->send(new AppointmentCancelledMail($this->record));
                    Notification::make()->title('Rendez-vous annulé ! Email envoyé.')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            EditAction::make()->label('Modifier'),
        ];
    }
}

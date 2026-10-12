<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContactMessage extends EditRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function getTitle(): string
    {
        return 'Lire le message';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Marquer automatiquement comme lu à l'ouverture
        $this->getRecord()->update(['is_read' => true]);
        $data['is_read'] = true;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Supprimer'),
        ];
    }
}

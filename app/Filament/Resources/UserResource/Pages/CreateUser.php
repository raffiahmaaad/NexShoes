<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Jika email_verified_at tidak diisi, set ke now() untuk user baru
        if (empty($data['email_verified_at'])) {
            $data['email_verified_at'] = now();
        }

        return $data;
    }
}

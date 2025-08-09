<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('verifyAllEmails')
                ->label('Verify All Unverified Emails')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function (): void {
                    $unverifiedCount = User::whereNull('email_verified_at')->count();

                    if ($unverifiedCount === 0) {
                        Notification::make()
                            ->title('All emails are already verified')
                            ->info()
                            ->send();
                        return;
                    }

                    User::whereNull('email_verified_at')->update([
                        'email_verified_at' => now()
                    ]);

                    Notification::make()
                        ->title("Successfully verified {$unverifiedCount} emails")
                        ->success()
                        ->send();

                    // Menggunakan redirect untuk merefresh halaman
                    $this->redirect(request()->header('Referer'));
                })
                ->requiresConfirmation()
                ->modalDescription('This will mark all unverified user emails as verified. Are you sure?'),
        ];
    }
}

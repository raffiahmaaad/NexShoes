<?php

namespace App\Filament\Resources\UserResource\Tabs;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;

class Customers extends Tab
{
    public function getLabel(): ?string
    {
        return 'Customers';
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->where('role', UserRole::Customer);
    }
}

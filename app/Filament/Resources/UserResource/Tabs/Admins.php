<?php

namespace App\Filament\Resources\UserResource\Tabs;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;

class Admins extends Tab
{
    public function getLabel(): ?string
    {
        return 'Admins';
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->where('role', UserRole::Admin);
    }
}

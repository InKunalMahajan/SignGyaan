<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class UserManagementService
{
    /**
     * Roles currently supported by SignGyaan.
     *
     * @return array<string, string>
     */
    public function roles(): array
    {
        return [
            User::ROLE_LEARNER => 'Learner',
            User::ROLE_ADMIN => 'Administrator',
        ];
    }

    /**
     * Account statuses reserved for the user-management system.
     * Status enforcement is introduced in the dedicated account-status step.
     *
     * @return array<string, string>
     */
    public function statuses(): array
    {
        return [
            User::STATUS_ACTIVE => 'Active',
            User::STATUS_SUSPENDED => 'Suspended',
            User::STATUS_DISABLED => 'Disabled',
        ];
    }

    /**
     * Return display-ready options for admin forms.
     *
     * @return Collection<int, array{value:string,label:string}>
     */
    public function roleOptions(): Collection
    {
        return collect($this->roles())
            ->map(fn (string $label, string $value) => compact('value', 'label'))
            ->values();
    }

    /**
     * @return Collection<int, array{value:string,label:string}>
     */
    public function statusOptions(): Collection
    {
        return collect($this->statuses())
            ->map(fn (string $label, string $value) => compact('value', 'label'))
            ->values();
    }
}

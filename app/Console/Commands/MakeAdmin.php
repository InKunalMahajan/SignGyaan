<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'user:make-admin {email : Email address of the existing SignGyaan user}';

    protected $description = 'Promote an existing SignGyaan user to the admin role';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (! $user) {
            $this->error('No SignGyaan user was found with that email address.');

            return self::FAILURE;
        }

        if ($user->isAdmin()) {
            $this->info($user->email . ' is already an admin.');

            return self::SUCCESS;
        }

        $user->role = 'admin';
        $user->save();

        $this->info($user->email . ' is now a SignGyaan admin.');

        return self::SUCCESS;
    }
}

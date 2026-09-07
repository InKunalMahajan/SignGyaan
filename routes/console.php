<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:make-admin {email}', function (string $email) {
    $user = User::where('email', $email)->first();

    if (! $user) {
        $this->error('No user was found with that email address.');

        return 1;
    }

    $user->update([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $this->info("{$user->name} is now an active Admin.");

    return 0;
})->purpose('Promote an existing SignGyaan user to Admin');

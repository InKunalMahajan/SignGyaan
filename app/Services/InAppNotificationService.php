<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SignGyaanNotification;

class InAppNotificationService
{
    public function send(
        User $user,
        string $category,
        string $title,
        string $message,
        ?string $url = null,
        ?string $actionLabel = null,
        array $meta = [],
    ): void {
        $user->notify(new SignGyaanNotification(
            category: $category,
            title: $title,
            message: $message,
            url: $url,
            actionLabel: $actionLabel,
            meta: $meta,
        ));
    }

    public function sendOnce(
        User $user,
        string $dedupeKey,
        string $category,
        string $title,
        string $message,
        ?string $url = null,
        ?string $actionLabel = null,
        array $meta = [],
    ): bool {
        $alreadySent = $user->notifications()
            ->where('data->meta->dedupe_key', $dedupeKey)
            ->exists();

        if ($alreadySent) {
            return false;
        }

        $this->send(
            user: $user,
            category: $category,
            title: $title,
            message: $message,
            url: $url,
            actionLabel: $actionLabel,
            meta: array_merge($meta, ['dedupe_key' => $dedupeKey]),
        );

        return true;
    }
}

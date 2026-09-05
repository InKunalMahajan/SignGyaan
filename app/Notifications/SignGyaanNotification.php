<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SignGyaanNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $category,
        private readonly string $title,
        private readonly string $message,
        private readonly ?string $url = null,
        private readonly ?string $actionLabel = null,
        private readonly array $meta = [],
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'category' => $this->category,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'action_label' => $this->actionLabel,
            'meta' => $this->meta,
        ];
    }
}

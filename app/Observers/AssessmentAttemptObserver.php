<?php

namespace App\Observers;

use App\Models\AssessmentAttempt;
use App\Services\AssessmentNotificationService;

class AssessmentAttemptObserver
{
    public function updated(AssessmentAttempt $attempt): void
    {
        if (! $attempt->wasChanged('status') || $attempt->status !== 'submitted') {
            return;
        }

        app(AssessmentNotificationService::class)->submitted($attempt);
    }
}

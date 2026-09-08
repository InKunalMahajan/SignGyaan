<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentLearnerLink extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'approved', 'declined'];

    public const RELATIONSHIPS = ['parent', 'guardian', 'family'];

    protected $fillable = [
        'parent_user_id',
        'learner_user_id',
        'relationship',
        'status',
        'responded_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'learner_user_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}

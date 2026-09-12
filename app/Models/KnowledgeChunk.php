<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeChunk extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_source_id',
        'chunk_index',
        'content',
        'embedding',
        'character_count',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'chunk_index' => 'integer',
            'embedding' => 'array',
            'character_count' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(KnowledgeSource::class, 'knowledge_source_id');
    }
}

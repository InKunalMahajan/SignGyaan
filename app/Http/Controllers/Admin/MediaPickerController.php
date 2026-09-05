<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaPickerController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'in:image,video,audio,document'],
            'isl' => ['nullable', 'boolean'],
        ]);

        $query = MediaAsset::query()
            ->select([
                'id', 'title', 'media_type', 'is_isl', 'source', 'file_path',
                'external_url', 'alt_text', 'caption', 'duration_seconds',
                'file_size', 'is_published', 'created_at',
            ]);

        if ($search = trim((string) ($validated['q'] ?? ''))) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%")
                    ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        if (! empty($validated['type'])) {
            $query->where('media_type', $validated['type']);
        }

        if ($request->has('isl')) {
            $query->where('is_isl', $request->boolean('isl'));
        }

        $assets = $query
            ->orderByDesc('is_published')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(fn (MediaAsset $asset) => [
                'id' => $asset->id,
                'title' => $asset->title,
                'media_type' => $asset->media_type,
                'is_isl' => $asset->is_isl,
                'is_published' => $asset->is_published,
                'url' => $asset->publicUrl(),
                'alt_text' => $asset->alt_text,
                'caption' => $asset->caption,
                'duration' => $asset->formattedDuration(),
                'file_size' => $asset->formattedFileSize(),
            ]);

        return response()->json(['data' => $assets]);
    }
}

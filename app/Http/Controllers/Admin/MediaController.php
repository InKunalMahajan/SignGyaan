<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MediaController extends Controller
{
    private const TYPES = ['image', 'video', 'document', 'audio', 'link'];

    private const UPLOAD_EXTENSIONS = [
        'image' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
        'video' => ['mp4', 'webm', 'mov'],
        'document' => ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'csv'],
        'audio' => ['mp3', 'wav', 'm4a'],
    ];

    public function index(Request $request): View
    {
        $query = MediaAsset::query()->with('uploader');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));

            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('original_name', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%")
                    ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('media_type', $request->input('type'));
        }

        if ($request->input('isl') === 'yes') {
            $query->where('media_type', 'video')->where('is_isl', true);
        } elseif ($request->input('isl') === 'no') {
            $query->where(function ($builder) {
                $builder->where('media_type', '!=', 'video')->orWhere('is_isl', false);
            });
        }

        if ($request->filled('source')) {
            $query->where('source', $request->input('source'));
        }

        if ($request->input('status') === 'published') {
            $query->where('is_published', true);
        } elseif ($request->input('status') === 'draft') {
            $query->where('is_published', false);
        }

        return view('admin.media.index', [
            'assets' => $query
                ->latest('updated_at')
                ->paginate(24)
                ->withQueryString(),
            'totalAssets' => MediaAsset::query()->count(),
            'imageCount' => MediaAsset::query()->where('media_type', 'image')->count(),
            'videoCount' => MediaAsset::query()->where('media_type', 'video')->count(),
            'documentCount' => MediaAsset::query()->where('media_type', 'document')->count(),
            'publishedAssets' => MediaAsset::query()->where('is_published', true)->count(),
            'types' => self::TYPES,
        ]);
    }

    public function create(): View
    {
        return view('admin.media.create', [
            'types' => self::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAsset($request);
        $validated['uploaded_by'] = $request->user()->id;
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_isl'] = $validated['media_type'] === 'video' && $request->boolean('is_isl');

        if ($validated['source'] === 'upload') {
            $file = $request->file('file');
            $this->ensureUploadMatchesType($file, $validated['media_type']);
            $validated = array_merge($validated, $this->storeUploadedFile($file, $validated['media_type']));
            $validated['external_url'] = null;
        } else {
            $validated['file_path'] = null;
            $validated['original_name'] = null;
            $validated['mime_type'] = null;
            $validated['file_size'] = null;
        }

        unset($validated['file']);

        MediaAsset::create($validated);

        return redirect()
            ->route('admin.media.index')
            ->with('status', 'Media item added successfully.');
    }

    public function edit(MediaAsset $mediaAsset): View
    {
        return view('admin.media.edit', [
            'mediaAsset' => $mediaAsset,
            'types' => self::TYPES,
        ]);
    }

    public function update(Request $request, MediaAsset $mediaAsset): RedirectResponse
    {
        $validated = $this->validateAsset($request, $mediaAsset);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_isl'] = $validated['media_type'] === 'video' && $request->boolean('is_isl');

        if ($validated['source'] === 'external') {
            $this->deleteStoredFile($mediaAsset);
            $validated['file_path'] = null;
            $validated['original_name'] = null;
            $validated['mime_type'] = null;
            $validated['file_size'] = null;
        } else {
            $validated['external_url'] = null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $this->ensureUploadMatchesType($file, $validated['media_type']);
                $this->deleteStoredFile($mediaAsset);
                $validated = array_merge($validated, $this->storeUploadedFile($file, $validated['media_type']));
            } else {
                $this->ensureExistingUploadMatchesType($mediaAsset, $validated['media_type']);
            }
        }

        unset($validated['file']);

        $mediaAsset->update($validated);

        return redirect()
            ->route('admin.media.index')
            ->with('status', 'Media item updated successfully.');
    }

    public function destroy(MediaAsset $mediaAsset): RedirectResponse
    {
        if ($mediaAsset->lessonVideoUses()->exists() || $mediaAsset->practiceResourceUses()->exists()) {
            return redirect()
                ->route('admin.media.index')
                ->with('status', 'This media item is linked to learning content. Unlink it from lessons or resources before deleting it.');
        }

        $this->deleteStoredFile($mediaAsset);
        $mediaAsset->delete();

        return redirect()
            ->route('admin.media.index')
            ->with('status', 'Media item deleted successfully.');
    }

    private function validateAsset(Request $request, ?MediaAsset $mediaAsset = null): array
    {
        $source = (string) $request->input('source');
        $needsUpload = $source === 'upload' && (! $mediaAsset || ! $mediaAsset->file_path);

        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'media_type' => ['required', Rule::in(self::TYPES)],
            'source' => ['required', Rule::in(['upload', 'external'])],
            'file' => [
                $needsUpload ? 'required' : 'nullable',
                'file',
                'max:51200',
                'mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,csv,mp3,wav,m4a',
            ],
            'external_url' => [$source === 'external' ? 'required' : 'nullable', 'url', 'max:2048'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:5000'],
            'language_code' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9-]+$/'],
            'duration_seconds' => ['nullable', 'integer', 'min:1', 'max:86400'],
        ]);
    }

    private function ensureUploadMatchesType(UploadedFile $file, string $mediaType): void
    {
        if ($mediaType === 'link' || ! isset(self::UPLOAD_EXTENSIONS[$mediaType])) {
            throw ValidationException::withMessages([
                'media_type' => 'Uploaded files must use Image, Video, Document or Audio as the media type.',
            ]);
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, self::UPLOAD_EXTENSIONS[$mediaType], true)) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file does not match the selected media type.',
            ]);
        }
    }

    private function ensureExistingUploadMatchesType(MediaAsset $mediaAsset, string $mediaType): void
    {
        if (! $mediaAsset->file_path) {
            return;
        }

        if ($mediaType === 'link' || ! isset(self::UPLOAD_EXTENSIONS[$mediaType])) {
            throw ValidationException::withMessages([
                'media_type' => 'Uploaded files must use Image, Video, Document or Audio as the media type.',
            ]);
        }

        $filename = $mediaAsset->original_name ?: $mediaAsset->file_path;
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (! in_array($extension, self::UPLOAD_EXTENSIONS[$mediaType], true)) {
            throw ValidationException::withMessages([
                'media_type' => 'Choose a media type that matches the current uploaded file, or upload a replacement file.',
            ]);
        }
    }

    private function storeUploadedFile(UploadedFile $file, string $mediaType): array
    {
        $path = $file->store('media/'.$mediaType, 'public');

        return [
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ];
    }

    private function deleteStoredFile(MediaAsset $mediaAsset): void
    {
        if ($mediaAsset->source === 'upload' && $mediaAsset->file_path) {
            Storage::disk('public')->delete($mediaAsset->file_path);
        }
    }
}

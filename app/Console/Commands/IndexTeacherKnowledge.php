<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\User;
use App\Services\RagIndexService;
use Illuminate\Console\Command;
use RuntimeException;

class IndexTeacherKnowledge extends Command
{
    protected $signature = 'rag:index-lessons {teacher : Teacher user ID} {--course= : Optional course ID}';

    protected $description = 'Index published lessons into a teacher RAG knowledge base.';

    public function handle(RagIndexService $indexer): int
    {
        $teacher = User::query()
            ->whereKey((int) $this->argument('teacher'))
            ->where('role', 'teacher')
            ->first();

        if (! $teacher) {
            $this->error('Teacher account not found.');

            return self::FAILURE;
        }

        $courses = Course::query()
            ->where('created_by', $teacher->id)
            ->where('is_active', true)
            ->when($this->option('course'), fn ($query, $courseId) => $query->whereKey((int) $courseId))
            ->with(['units' => function ($query): void {
                $query->where('is_active', true)
                    ->with(['lessons' => fn ($lessons) => $lessons->where('status', 'published')]);
            }])
            ->get();

        if ($courses->isEmpty()) {
            $this->warn('No matching active courses were found for this teacher.');

            return self::SUCCESS;
        }

        $indexed = 0;
        $skipped = 0;

        foreach ($courses as $course) {
            foreach ($course->units as $unit) {
                foreach ($unit->lessons as $lesson) {
                    try {
                        $indexer->syncLesson($teacher, $lesson);
                        $indexed++;
                        $this->line('Indexed: '.$course->title.' / '.$lesson->title);
                    } catch (RuntimeException $exception) {
                        $skipped++;
                        $this->warn('Skipped '.$lesson->title.': '.$exception->getMessage());
                    }
                }
            }
        }

        $this->info("Finished. {$indexed} lesson(s) indexed; {$skipped} skipped.");

        return self::SUCCESS;
    }
}

<?php

namespace Tests\Feature;

use App\Ai\Agents\TeacherRagAssistant;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\KnowledgeSource;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Prompts\EmbeddingsPrompt;
use Tests\TestCase;

class TeacherRagPhase12Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'signgyaan-ai.rag.embedding_dimensions' => 3,
            'signgyaan-ai.rag.minimum_similarity' => 0.1,
            'signgyaan-ai.rag.top_k' => 3,
            'signgyaan-ai.rag.chunk_characters' => 5000,
            'signgyaan-ai.rag.chunk_overlap' => 0,
        ]);

        Embeddings::fake(function (EmbeddingsPrompt $prompt): array {
            return array_map(fn (): array => [1.0, 0.0, 0.0], $prompt->inputs);
        });
    }

    public function test_teacher_can_add_source_and_receive_grounded_answer_with_source_reference(): void
    {
        [$teacher, $course] = $this->teacherWithCourse('RAG');

        $this->actingAs($teacher)
            ->post(route('teacher.rag.sources.store'), [
                'title' => 'DBMS Primary Key Notes',
                'course_id' => $course->id,
                'content' => 'A primary key uniquely identifies each record in a database table. It should not contain duplicate values.',
            ])
            ->assertRedirect();

        $source = KnowledgeSource::where('title', 'DBMS Primary Key Notes')->firstOrFail();
        $this->assertSame('indexed', $source->status);
        $this->assertSame($teacher->id, $source->owner_id);
        $this->assertDatabaseCount('knowledge_chunks', 1);

        TeacherRagAssistant::fake([
            'A primary key uniquely identifies each record. [Source 1]',
        ])->preventStrayPrompts();

        $this->actingAs($teacher)
            ->post(route('teacher.rag.ask'), [
                'question' => 'What is a primary key?',
                'course_id' => $course->id,
            ])
            ->assertOk()
            ->assertSee('Grounded answer')
            ->assertSee('A primary key uniquely identifies each record.')
            ->assertSee('[Source 1]')
            ->assertSee('DBMS Primary Key Notes');

        TeacherRagAssistant::assertPrompted(fn (AgentPrompt $prompt): bool =>
            $prompt->contains('What is a primary key?')
            && $prompt->contains('DBMS Primary Key Notes')
            && $prompt->contains('[Source 1]')
        );
    }

    public function test_teacher_can_index_published_course_lesson_as_knowledge_source(): void
    {
        [$teacher, $course] = $this->teacherWithCourse('Lesson');
        $unit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Database Basics',
            'position' => 1,
            'is_active' => true,
        ]);
        $lesson = Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Primary Keys',
            'summary' => 'Primary keys identify rows uniquely.',
            'notes' => 'Use a student ID as a simple example.',
            'position' => 1,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.rag.courses.index', $course))
            ->assertRedirect();

        $this->assertDatabaseHas('knowledge_sources', [
            'owner_id' => $teacher->id,
            'course_id' => $course->id,
            'source_type' => 'lesson',
            'source_ref' => (string) $lesson->id,
            'status' => 'indexed',
        ]);
    }

    public function test_teacher_cannot_access_another_teachers_knowledge_source_or_course(): void
    {
        [$teacher] = $this->teacherWithCourse('Owner A');
        [$otherTeacher, $otherCourse] = $this->teacherWithCourse('Owner B');

        $source = KnowledgeSource::create([
            'owner_id' => $otherTeacher->id,
            'course_id' => $otherCourse->id,
            'title' => 'Private source',
            'source_type' => 'manual',
            'content' => 'Private teacher knowledge.',
            'content_hash' => hash('sha256', 'Private teacher knowledge.'),
            'status' => 'draft',
        ]);

        $this->actingAs($teacher)
            ->delete(route('teacher.rag.sources.destroy', $source))
            ->assertForbidden();

        $this->actingAs($teacher)
            ->post(route('teacher.rag.courses.index', $otherCourse))
            ->assertForbidden();

        $this->assertDatabaseHas('knowledge_sources', ['id' => $source->id]);
    }

    public function test_learner_cannot_open_teacher_knowledge_assistant(): void
    {
        $learner = User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
        ]);

        $this->actingAs($learner)
            ->get(route('teacher.rag.index'))
            ->assertForbidden();
    }

    public function test_embedding_generation_is_used_for_source_and_question_retrieval(): void
    {
        [$teacher, $course] = $this->teacherWithCourse('Embedding');

        $this->actingAs($teacher)->post(route('teacher.rag.sources.store'), [
            'title' => 'Storage Notes',
            'course_id' => $course->id,
            'content' => 'SSD is a storage device with no moving mechanical parts.',
        ]);

        TeacherRagAssistant::fake(['SSD has no moving mechanical parts. [Source 1]']);

        $this->actingAs($teacher)->post(route('teacher.rag.ask'), [
            'question' => 'What is SSD?',
            'course_id' => $course->id,
        ]);

        Embeddings::assertGenerated(fn (EmbeddingsPrompt $prompt): bool => $prompt->contains('SSD is a storage device'));
        Embeddings::assertGenerated(fn (EmbeddingsPrompt $prompt): bool => $prompt->contains('What is SSD?'));
    }

    private function teacherWithCourse(string $suffix): array
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
        ]);
        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => "IT {$suffix}",
            'slug' => 'it-'.strtolower(str_replace(' ', '-', $suffix)).'-'.uniqid(),
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => "Course {$suffix}",
            'slug' => 'course-'.strtolower(str_replace(' ', '-', $suffix)).'-'.uniqid(),
            'level' => 'Beginner',
            'is_active' => true,
        ]);

        return [$teacher, $course];
    }
}

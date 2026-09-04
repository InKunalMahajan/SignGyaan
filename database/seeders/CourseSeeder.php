<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $catalogue = [
            'english' => [
                ['English Foundations', 'english-foundations', 'Beginner', 'Build vocabulary, grammar and simple sentence understanding for everyday use.', true],
                ['Everyday Communication', 'everyday-communication', 'Beginner', 'Practise useful words, sentences and communication for daily situations.', false],
                ['Reading & Understanding', 'reading-understanding', 'Intermediate', 'Strengthen reading comprehension, meaning and confident understanding of short texts.', false],
            ],
            'mathematics' => [
                ['Everyday Mathematics', 'everyday-mathematics', 'Beginner', 'Use numbers, money, time and calculations in practical everyday situations.', true],
                ['Numbers & Operations', 'numbers-operations', 'Beginner', 'Understand numbers and practise the main operations step by step.', false],
                ['Measurement & Percentages', 'measurement-percentages', 'Intermediate', 'Learn useful measurement, comparison, fractions and percentage skills.', false],
            ],
            'science' => [
                ['Science Foundations', 'science-foundations', 'Beginner', 'Build a clear foundation in observation, scientific thinking and core ideas.', false],
                ['Everyday Science', 'everyday-science', 'Beginner', 'Connect science concepts with familiar situations from daily life.', false],
                ['Living World', 'living-world', 'Intermediate', 'Explore plants, animals, people and how living things interact with their environment.', false],
            ],
            'digital-skills' => [
                ['Computer Basics', 'computer-basics', 'Beginner', 'Understand hardware, software, files, folders and everyday computer use.', true],
                ['Internet & Online Tools', 'internet-online-tools', 'Beginner', 'Learn safe internet use, browsers, search and useful online tools.', false],
                ['Office Productivity Skills', 'office-productivity-skills', 'Intermediate', 'Build practical document, spreadsheet and presentation skills.', false],
            ],
            'general-knowledge' => [
                ['India & the World', 'india-the-world', 'Beginner', 'Build awareness of India, countries, places, society and the wider world.', false],
                ['Everyday Awareness', 'everyday-awareness', 'Beginner', 'Understand useful information, services and situations encountered in daily life.', false],
                ['People, Places & Events', 'people-places-events', 'Intermediate', 'Learn about important people, places and events through short visual topics.', false],
            ],
            'life-skills' => [
                ['Everyday Life Skills', 'everyday-life-skills', 'Beginner', 'Develop practical routines, independence and confidence for everyday tasks.', false],
                ['Time & Task Management', 'time-task-management', 'Beginner', 'Learn simple ways to organise time, tasks, routines and priorities.', false],
                ['Communication & Confidence', 'communication-confidence', 'Intermediate', 'Strengthen communication, self-expression and confidence in everyday situations.', false],
            ],
        ];

        foreach ($catalogue as $subjectSlug => $courses) {
            $subject = Subject::query()->where('slug', $subjectSlug)->firstOrFail();

            foreach ($courses as $index => [$title, $slug, $level, $shortDescription, $featured]) {
                Course::query()->updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'slug' => $slug,
                    ],
                    [
                        'title' => $title,
                        'level' => $level,
                        'short_description' => $shortDescription,
                        'description' => $shortDescription . ' SignGyaan presents the course through structured units, visual explanations, simple notes and practice.',
                        'estimated_duration_minutes' => null,
                        'sort_order' => $index + 1,
                        'is_featured' => $featured,
                        'is_published' => true,
                    ]
                );
            }
        }
    }
}

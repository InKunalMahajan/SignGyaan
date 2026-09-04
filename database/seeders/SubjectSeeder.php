<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'English',
                'slug' => 'english',
                'short_description' => 'Build vocabulary, reading, grammar and everyday communication skills.',
                'description' => 'Structured English learning through visual explanations, simple notes, examples and practice.',
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'name' => 'Mathematics',
                'slug' => 'mathematics',
                'short_description' => 'Learn numbers, calculations, money, time, measurement and problem solving.',
                'description' => 'Practical mathematics learning focused on clear concepts and everyday use.',
                'sort_order' => 2,
                'is_published' => true,
            ],
            [
                'name' => 'Science',
                'slug' => 'science',
                'short_description' => 'Understand scientific ideas, the living world, matter, energy and daily science.',
                'description' => 'Visual science learning that connects core ideas with familiar examples and observations.',
                'sort_order' => 3,
                'is_published' => true,
            ],
            [
                'name' => 'Digital Skills',
                'slug' => 'digital-skills',
                'short_description' => 'Develop practical computer, internet, productivity and digital-safety skills.',
                'description' => 'Learn digital skills step by step through practical examples and accessible visual instruction.',
                'sort_order' => 4,
                'is_published' => true,
            ],
            [
                'name' => 'General Knowledge',
                'slug' => 'general-knowledge',
                'short_description' => 'Explore India, the world, people, places, society and everyday awareness.',
                'description' => 'Build useful general awareness through short, clear and visually supported learning topics.',
                'sort_order' => 5,
                'is_published' => true,
            ],
            [
                'name' => 'Life Skills',
                'slug' => 'life-skills',
                'short_description' => 'Strengthen communication, organisation, confidence and everyday independence.',
                'description' => 'Practical life-skills learning designed around daily routines, communication and independence.',
                'sort_order' => 6,
                'is_published' => true,
            ],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['slug' => $subject['slug']],
                $subject + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

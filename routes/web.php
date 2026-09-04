<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')
    ->name('home');

Route::view('/learn', 'pages.learn')
    ->name('learn');

Route::view('/subjects', 'pages.subjects')
    ->name('subjects');

Route::get('/subjects/{subject}', function (string $subject) {
    $subjects = [
        'english' => [
            'name' => 'English',
            'code' => 'Aa',
            'eyebrow' => 'Language & Communication',
            'description' => 'Build vocabulary, grammar, reading and everyday communication skills through visual and structured lessons.',
            'featured_course' => 'English Foundations',
            'featured_units' => 4,
            'featured_lessons' => 18,
            'courses' => [
                ['title' => 'English Foundations', 'level' => 'Beginner', 'description' => 'Build basic vocabulary, grammar and sentence understanding for everyday use.', 'units' => 4, 'lessons' => 18],
                ['title' => 'Everyday Communication', 'level' => 'Beginner', 'description' => 'Practise useful expressions, conversations and communication for daily situations.', 'units' => 3, 'lessons' => 15],
                ['title' => 'Reading & Understanding', 'level' => 'Intermediate', 'description' => 'Improve reading confidence with short texts, meaning, context and comprehension.', 'units' => 4, 'lessons' => 20],
            ],
            'outcomes' => ['Build useful everyday vocabulary', 'Understand basic grammar patterns', 'Improve reading comprehension', 'Communicate with greater confidence'],
        ],
        'mathematics' => [
            'name' => 'Mathematics',
            'code' => '123',
            'eyebrow' => 'Numbers & Problem Solving',
            'description' => 'Understand numbers, money, measurements and practical calculations through simple visual steps.',
            'featured_course' => 'Everyday Mathematics',
            'featured_units' => 4,
            'featured_lessons' => 20,
            'courses' => [
                ['title' => 'Everyday Mathematics', 'level' => 'Beginner', 'description' => 'Learn useful calculations for money, time, shopping and everyday situations.', 'units' => 4, 'lessons' => 20],
                ['title' => 'Numbers & Operations', 'level' => 'Beginner', 'description' => 'Strengthen addition, subtraction, multiplication, division and number sense.', 'units' => 4, 'lessons' => 18],
                ['title' => 'Measurement & Percentages', 'level' => 'Intermediate', 'description' => 'Use measurements, ratios and percentages in practical situations.', 'units' => 3, 'lessons' => 16],
            ],
            'outcomes' => ['Calculate with confidence', 'Use maths in daily life', 'Understand money and percentages', 'Solve problems step by step'],
        ],
        'science' => [
            'name' => 'Science',
            'code' => 'SCI',
            'eyebrow' => 'Explore & Understand',
            'description' => 'Explore scientific ideas with visual explanations, real-life examples and simple practice activities.',
            'featured_course' => 'Science Foundations',
            'featured_units' => 4,
            'featured_lessons' => 18,
            'courses' => [
                ['title' => 'Science Foundations', 'level' => 'Beginner', 'description' => 'Understand basic scientific ideas about matter, energy and the world around us.', 'units' => 4, 'lessons' => 18],
                ['title' => 'Everyday Science', 'level' => 'Beginner', 'description' => 'Connect science concepts to common objects, health, environment and daily life.', 'units' => 3, 'lessons' => 15],
                ['title' => 'Living World', 'level' => 'Intermediate', 'description' => 'Explore plants, animals, the human body and ecosystems visually.', 'units' => 4, 'lessons' => 19],
            ],
            'outcomes' => ['Understand core science ideas', 'Connect science to daily life', 'Learn through visual examples', 'Build observation and reasoning skills'],
        ],
        'digital-skills' => [
            'name' => 'Digital Skills',
            'code' => 'PC',
            'eyebrow' => 'Computers & Technology',
            'description' => 'Learn computers, software, internet tools and practical digital tasks through clear visual guidance.',
            'featured_course' => 'Computer Basics',
            'featured_units' => 5,
            'featured_lessons' => 24,
            'courses' => [
                ['title' => 'Computer Basics', 'level' => 'Beginner', 'description' => 'Understand hardware, software, files, folders and basic computer use.', 'units' => 5, 'lessons' => 24],
                ['title' => 'Internet & Online Tools', 'level' => 'Beginner', 'description' => 'Learn browsing, email, online safety and useful digital tools.', 'units' => 4, 'lessons' => 18],
                ['title' => 'Office & Productivity Skills', 'level' => 'Intermediate', 'description' => 'Build practical document, spreadsheet and presentation skills.', 'units' => 5, 'lessons' => 22],
            ],
            'outcomes' => ['Use a computer confidently', 'Manage files and folders', 'Use internet tools safely', 'Build practical digital productivity skills'],
        ],
        'general-knowledge' => [
            'name' => 'General Knowledge',
            'code' => 'GK',
            'eyebrow' => 'India, World & Society',
            'description' => 'Build useful knowledge about India, the world, society, places, people and everyday topics.',
            'featured_course' => 'India & the World',
            'featured_units' => 4,
            'featured_lessons' => 18,
            'courses' => [
                ['title' => 'India & the World', 'level' => 'Beginner', 'description' => 'Learn important places, people, symbols and facts about India and the world.', 'units' => 4, 'lessons' => 18],
                ['title' => 'Everyday Awareness', 'level' => 'Beginner', 'description' => 'Build useful awareness about society, services, environment and daily life.', 'units' => 3, 'lessons' => 15],
                ['title' => 'People, Places & Events', 'level' => 'Intermediate', 'description' => 'Explore important people, locations and events through visual summaries.', 'units' => 4, 'lessons' => 20],
            ],
            'outcomes' => ['Know key facts about India', 'Understand the wider world', 'Build everyday awareness', 'Connect topics through visual learning'],
        ],
        'life-skills' => [
            'name' => 'Life Skills',
            'code' => 'LS',
            'eyebrow' => 'Confidence & Everyday Skills',
            'description' => 'Develop communication, organisation, confidence and practical skills for everyday independence.',
            'featured_course' => 'Everyday Life Skills',
            'featured_units' => 4,
            'featured_lessons' => 16,
            'courses' => [
                ['title' => 'Everyday Life Skills', 'level' => 'Beginner', 'description' => 'Build practical habits for communication, organisation and daily independence.', 'units' => 4, 'lessons' => 16],
                ['title' => 'Time & Task Management', 'level' => 'Beginner', 'description' => 'Learn to plan your day, prioritise tasks and manage time effectively.', 'units' => 3, 'lessons' => 14],
                ['title' => 'Communication & Confidence', 'level' => 'Intermediate', 'description' => 'Develop clearer communication, self-expression and confidence in everyday situations.', 'units' => 4, 'lessons' => 18],
            ],
            'outcomes' => ['Plan and organise daily tasks', 'Communicate more confidently', 'Build independence', 'Develop practical everyday habits'],
        ],
    ];

    abort_unless(isset($subjects[$subject]), 404);

    return view('pages.subject', [
        'subject' => $subjects[$subject],
        'slug' => $subject,
    ]);
})->name('subjects.show');

Route::view('/explore', 'pages.explore')
    ->name('explore');

Route::view('/about', 'pages.about')
    ->name('about');

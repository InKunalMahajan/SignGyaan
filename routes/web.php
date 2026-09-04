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

Route::get('/subjects/{subject}/courses/{course}', function (string $subject, string $course) {
    $catalog = [
        'english' => [
            'subject' => ['name' => 'English', 'code' => 'Aa'],
            'courses' => [
                'english-foundations' => ['title' => 'English Foundations', 'level' => 'Beginner', 'description' => 'Build basic vocabulary, grammar and sentence understanding for everyday use.', 'units' => 4, 'lessons' => 18, 'outcomes' => ['Build everyday vocabulary', 'Understand basic sentence patterns', 'Use essential grammar confidently', 'Read and understand simple English']],
                'everyday-communication' => ['title' => 'Everyday Communication', 'level' => 'Beginner', 'description' => 'Practise useful expressions, conversations and communication for daily situations.', 'units' => 3, 'lessons' => 15, 'outcomes' => ['Use common daily expressions', 'Understand simple conversations', 'Respond clearly in everyday situations', 'Build communication confidence']],
                'reading-understanding' => ['title' => 'Reading & Understanding', 'level' => 'Intermediate', 'description' => 'Improve reading confidence with short texts, meaning, context and comprehension.', 'units' => 4, 'lessons' => 20, 'outcomes' => ['Read short texts with confidence', 'Identify key information', 'Understand meaning from context', 'Answer comprehension questions']],
            ],
        ],
        'mathematics' => [
            'subject' => ['name' => 'Mathematics', 'code' => '123'],
            'courses' => [
                'everyday-mathematics' => ['title' => 'Everyday Mathematics', 'level' => 'Beginner', 'description' => 'Learn useful calculations for money, time, shopping and everyday situations.', 'units' => 4, 'lessons' => 20, 'outcomes' => ['Calculate with everyday numbers', 'Use maths with money and shopping', 'Understand time and simple measurement', 'Solve practical problems step by step']],
                'numbers-operations' => ['title' => 'Numbers & Operations', 'level' => 'Beginner', 'description' => 'Strengthen addition, subtraction, multiplication, division and number sense.', 'units' => 4, 'lessons' => 18, 'outcomes' => ['Understand number value', 'Add and subtract confidently', 'Use multiplication and division', 'Choose the correct operation']],
                'measurement-percentages' => ['title' => 'Measurement & Percentages', 'level' => 'Intermediate', 'description' => 'Use measurements, ratios and percentages in practical situations.', 'units' => 3, 'lessons' => 16, 'outcomes' => ['Use common units of measurement', 'Compare quantities and ratios', 'Calculate basic percentages', 'Apply measurement in daily life']],
            ],
        ],
        'science' => [
            'subject' => ['name' => 'Science', 'code' => 'SCI'],
            'courses' => [
                'science-foundations' => ['title' => 'Science Foundations', 'level' => 'Beginner', 'description' => 'Understand basic scientific ideas about matter, energy and the world around us.', 'units' => 4, 'lessons' => 18, 'outcomes' => ['Understand basic scientific ideas', 'Recognise matter and energy concepts', 'Use observation to learn', 'Connect science to everyday examples']],
                'everyday-science' => ['title' => 'Everyday Science', 'level' => 'Beginner', 'description' => 'Connect science concepts to common objects, health, environment and daily life.', 'units' => 3, 'lessons' => 15, 'outcomes' => ['Recognise science in daily life', 'Understand simple health concepts', 'Explore common materials and objects', 'Build practical scientific awareness']],
                'living-world' => ['title' => 'Living World', 'level' => 'Intermediate', 'description' => 'Explore plants, animals, the human body and ecosystems visually.', 'units' => 4, 'lessons' => 19, 'outcomes' => ['Identify living organisms', 'Understand basic body systems', 'Explore plants and animals', 'Recognise ecosystem relationships']],
            ],
        ],
        'digital-skills' => [
            'subject' => ['name' => 'Digital Skills', 'code' => 'PC'],
            'courses' => [
                'computer-basics' => ['title' => 'Computer Basics', 'level' => 'Beginner', 'description' => 'Understand hardware, software, files, folders and basic computer use.', 'units' => 5, 'lessons' => 24, 'outcomes' => ['Identify basic computer hardware', 'Understand software and operating systems', 'Manage files and folders', 'Use a computer with confidence']],
                'internet-online-tools' => ['title' => 'Internet & Online Tools', 'level' => 'Beginner', 'description' => 'Learn browsing, email, online safety and useful digital tools.', 'units' => 4, 'lessons' => 18, 'outcomes' => ['Browse the web effectively', 'Use email and online tools', 'Recognise online safety risks', 'Protect personal information']],
                'office-productivity-skills' => ['title' => 'Office & Productivity Skills', 'level' => 'Intermediate', 'description' => 'Build practical document, spreadsheet and presentation skills.', 'units' => 5, 'lessons' => 22, 'outcomes' => ['Create clear documents', 'Use spreadsheets for simple data', 'Build useful presentations', 'Organise digital work efficiently']],
            ],
        ],
        'general-knowledge' => [
            'subject' => ['name' => 'General Knowledge', 'code' => 'GK'],
            'courses' => [
                'india-the-world' => ['title' => 'India & the World', 'level' => 'Beginner', 'description' => 'Learn important places, people, symbols and facts about India and the world.', 'units' => 4, 'lessons' => 18, 'outcomes' => ['Recognise important places', 'Know major national symbols', 'Build world awareness', 'Connect facts using visual summaries']],
                'everyday-awareness' => ['title' => 'Everyday Awareness', 'level' => 'Beginner', 'description' => 'Build useful awareness about society, services, environment and daily life.', 'units' => 3, 'lessons' => 15, 'outcomes' => ['Understand common public services', 'Build social awareness', 'Recognise environmental issues', 'Use knowledge in everyday decisions']],
                'people-places-events' => ['title' => 'People, Places & Events', 'level' => 'Intermediate', 'description' => 'Explore important people, locations and events through visual summaries.', 'units' => 4, 'lessons' => 20, 'outcomes' => ['Identify notable people', 'Recognise important places', 'Understand key events', 'Connect people, places and time']],
            ],
        ],
        'life-skills' => [
            'subject' => ['name' => 'Life Skills', 'code' => 'LS'],
            'courses' => [
                'everyday-life-skills' => ['title' => 'Everyday Life Skills', 'level' => 'Beginner', 'description' => 'Build practical habits for communication, organisation and daily independence.', 'units' => 4, 'lessons' => 16, 'outcomes' => ['Build useful daily routines', 'Organise everyday tasks', 'Communicate needs clearly', 'Develop greater independence']],
                'time-task-management' => ['title' => 'Time & Task Management', 'level' => 'Beginner', 'description' => 'Learn to plan your day, prioritise tasks and manage time effectively.', 'units' => 3, 'lessons' => 14, 'outcomes' => ['Plan a daily schedule', 'Prioritise important tasks', 'Break work into simple steps', 'Use time more effectively']],
                'communication-confidence' => ['title' => 'Communication & Confidence', 'level' => 'Intermediate', 'description' => 'Develop clearer communication, self-expression and confidence in everyday situations.', 'units' => 4, 'lessons' => 18, 'outcomes' => ['Express ideas more clearly', 'Build confidence in communication', 'Handle everyday conversations', 'Practise respectful self-advocacy']],
            ],
        ],
    ];

    abort_unless(isset($catalog[$subject]), 404);
    abort_unless(isset($catalog[$subject]['courses'][$course]), 404);

    return view('pages.course', [
        'subject' => $catalog[$subject]['subject'],
        'course' => $catalog[$subject]['courses'][$course],
        'subjectSlug' => $subject,
        'courseSlug' => $course,
    ]);
})->name('courses.show');

Route::view('/explore', 'pages.explore')
    ->name('explore');

Route::view('/about', 'pages.about')
    ->name('about');

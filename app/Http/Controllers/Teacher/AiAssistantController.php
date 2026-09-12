<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\TeacherAiService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AiAssistantController extends Controller
{
    public function index(): View
    {
        return view('teacher.ai.index', [
            'tasks' => TeacherAiService::TASKS,
            'configured' => filled(config('ai.providers.openai.key')),
            'provider' => config('signgyaan-ai.teacher.provider', 'openai'),
            'model' => config('signgyaan-ai.teacher.model', 'gpt-5.6-luna'),
            'result' => null,
        ]);
    }

    public function generate(Request $request, TeacherAiService $ai): View
    {
        $validated = $request->validate([
            'task' => ['required', 'string', 'in:'.implode(',', array_keys(TeacherAiService::TASKS))],
            'prompt' => ['required', 'string', 'min:5', 'max:'.config('signgyaan-ai.teacher.max_prompt_characters', 4000)],
            'context' => ['nullable', 'string', 'max:'.config('signgyaan-ai.teacher.max_context_characters', 2000)],
        ]);

        try {
            $result = $ai->generate(
                $request->user(),
                $validated['task'],
                $validated['prompt'],
                $validated['context'] ?? null,
            );
        } catch (RuntimeException $exception) {
            return view('teacher.ai.index', [
                'tasks' => TeacherAiService::TASKS,
                'configured' => filled(config('ai.providers.openai.key')),
                'provider' => config('signgyaan-ai.teacher.provider', 'openai'),
                'model' => config('signgyaan-ai.teacher.model', 'gpt-5.6-luna'),
                'result' => null,
            ])->withErrors(['ai' => $exception->getMessage()]);
        }

        return view('teacher.ai.index', [
            'tasks' => TeacherAiService::TASKS,
            'configured' => filled(config('ai.providers.openai.key')),
            'provider' => config('signgyaan-ai.teacher.provider', 'openai'),
            'model' => config('signgyaan-ai.teacher.model', 'gpt-5.6-luna'),
            'result' => $result,
        ]);
    }
}

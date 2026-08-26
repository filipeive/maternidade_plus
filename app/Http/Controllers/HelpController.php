<?php

namespace App\Http\Controllers;

use App\Services\AiAssistantService;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    protected AiAssistantService $aiService;

    public function __construct(AiAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        return view('help.index');
    }

    public function manual()
    {
        return view('help.manual');
    }

    public function faq()
    {
        return view('help.faq');
    }

    public function askAi(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'history' => 'nullable|array'
        ]);

        $prompt = $request->input('prompt');
        $history = $request->input('history', []);

        $result = $this->aiService->ask($prompt, $history);

        if ($result[0] === true) {
            return response()->json([
                'success' => true,
                'response' => $result[1]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result[1]
        ], 400);
    }
}

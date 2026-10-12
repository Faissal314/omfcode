<?php

namespace App\Http\Controllers;

use App\Models\FaqQuestion;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqQuestions = FaqQuestion::where('is_public', true)
            ->whereNotNull('answer')
            ->latest()
            ->take(20)
            ->get();

        return view('faq', [
            'faqQuestions' => $faqQuestions,
        ]);
    }

    public function ask(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $question = trim($validated['question']);

        $chatRequest = new Request([
            'message' => $question,
            'history' => [],
        ]);

        $response = app(ChatBotController::class)->chat($chatRequest);
        $payload = $response->getData(true);
        $answer = $payload['reply'] ?? 'Je suis désolé, je n’ai pas pu formuler de réponse pour le moment.';

        $faq = FaqQuestion::create([
            'question' => $question,
            'answer' => $answer,
            'source' => 'ai',
            'is_public' => true,
        ]);

        return response()->json([
            'success' => true,
            'answer' => $answer,
            'faq' => [
                'question' => $faq->question,
                'answer' => $faq->answer,
                'created_at' => $faq->created_at->toDateTimeString(),
            ],
        ]);
    }
}

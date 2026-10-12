<?php

use App\Models\FaqQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('faq page renders public questions', function (): void {
    FaqQuestion::create([
        'question' => 'Quelle est votre expertise ?',
        'answer' => 'Nous sommes spécialisés en sites web, IA et automatisation.',
        'source' => 'seed',
        'is_public' => true,
    ]);

    $response = $this->get('/faq');

    $response->assertStatus(200);
    $response->assertSee('Questions fréquentes');
    $response->assertSee('Quelle est votre expertise ?');
});

test('faq ask route returns answer and stores question', function (): void {
    Http::fake([
        'api.groq.com/*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Nous pouvons vous aider avec votre stratégie digitale.',
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->postJson('/faq/ask', [
        'question' => 'Que proposez-vous ?',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'answer',
            'faq' => ['question', 'answer', 'created_at'],
        ])
        ->assertJson([
            'success' => true,
            'faq' => [
                'question' => 'Que proposez-vous ?',
            ],
        ]);

    $this->assertDatabaseHas('faq_questions', [
        'question' => 'Que proposez-vous ?',
        'source' => 'ai',
    ]);
});

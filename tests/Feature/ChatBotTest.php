<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('chatbot returns response with groq api fake', function (): void {
    config(['services.groq.key' => 'fake-groq-key']);

    Http::fake([
        'api.groq.com/*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Bonjour, OMF est à votre service pour vos projets web.',
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->postJson('/chatbot', [
        'message' => 'Bonjour',
        'history' => [],
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'reply' => 'Bonjour, OMF est à votre service pour vos projets web.',
        ]);
});

test('chatbot uses smart fallback when groq api key is not set', function (): void {
    config(['services.groq.key' => null]);

    $response = $this->postJson('/chatbot', [
        'message' => 'Quels sont vos tarifs ?',
    ]);

    $response->assertSuccessful();
    expect($response->json('reply'))->toContain('devis');
});

test('chatbot handles services question fallback', function (): void {
    config(['services.groq.key' => null]);

    $response = $this->postJson('/chatbot', [
        'message' => 'Quels sont vos services ?',
    ]);

    $response->assertSuccessful();
    expect($response->json('reply'))->toContain('Développement');
});

<?php

use App\Filament\Resources\Posts\Schemas\PostForm;
use App\Models\Post;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('blog page renders empty state when no posts exist', function (): void {
    $response = $this->get('/blog');

    $response->assertStatus(200);
    $response->assertSee('Articles en préparation !');
});

test('post form can be configured without reflection or missing class errors', function (): void {
    $schema = Schema::make();
    $configured = PostForm::configure($schema);

    expect($configured)->toBeInstanceOf(Schema::class);
    expect($configured->getComponents())->not->toBeEmpty();
});

test('can create post and view it on blog index and detail pages', function (): void {
    $post = Post::create([
        'title' => 'Article sur l\'intelligence artificielle',
        'slug' => 'article-sur-l-intelligence-artificielle',
        'content' => '<p>Voici un contenu détaillé de test pour notre article de blog.</p>',
        'category' => 'IA & Tech',
        'read_time' => '5 min',
        'is_featured' => true,
    ]);

    expect($post->exists)->toBeTrue();

    // Blog listing
    $listResponse = $this->get('/blog');
    $listResponse->assertStatus(200);
    $listResponse->assertSee('Article sur l\'intelligence artificielle');
    $listResponse->assertSee('IA &amp; TECH', escape: false);

    // Blog show
    $showResponse = $this->get('/blog/'.$post->slug);
    $showResponse->assertStatus(200);
    $showResponse->assertSee('Article sur l\'intelligence artificielle');
    $showResponse->assertSee('Voici un contenu détaillé de test pour notre article de blog.');

    // View count increments
    $post->refresh();
    expect($post->views)->toBe(1);
});

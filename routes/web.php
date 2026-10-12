<?php

use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\QuoteRequestController;
use App\Models\Appointment;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'projects' => Project::latest()->take(3)->get(),
        'testimonials' => Testimonial::where('is_approved', true)->latest()->take(3)->get(),
    ]);
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    ContactMessage::create($validated);

    return response()->json(['success' => true]);
});

Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::post('/faq/ask', [FaqController::class, 'ask'])->name('faq.ask');

Route::get('/rendez-vous', function () {
    return view('rendez-vous');
})->name('rendez-vous');

Route::post('/rendez-vous', function (Request $request) {
    $validated = $request->validate([
        'type' => 'required|string',
        'date' => 'required|date',
        'time' => 'required|string',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:255',
        'company' => 'nullable|string|max:255',
        'message' => 'nullable|string',
        'canal' => 'required|string',
    ]);

    $validated['contact_channel'] = $validated['canal'];
    unset($validated['canal']);

    Appointment::create($validated);

    return response()->json(['success' => true]);
});

Route::get('/services', function () {
    return view('services', [
        'categories' => ServiceCategory::with('services')->get(),
    ]);
})->name('services');

Route::get('/services/{slug}', function ($slug) {
    $service = Service::where('slug', $slug)->firstOrFail();

    return view('service-show', [
        'service' => $service,
    ]);
})->name('services.show');

Route::get('/realisations', function () {
    return view('realisations', [
        'projects' => Project::latest()->get(),
    ]);
})->name('realisations');

Route::get('/blog', function () {
    return view('blog', [
        'posts' => Post::latest()->get(),
        'featured' => Post::where('is_featured', true)->latest()->first(),
    ]);
})->name('blog');

Route::get('/blog/{slug}', function ($slug) {
    $post = Post::where('slug', $slug)->firstOrFail();

    // Incrémenter les vues une fois par session
    $visited = session()->get('visited_posts', []);
    if (! in_array($post->id, $visited)) {
        $post->increment('views');
        session()->push('visited_posts', $post->id);
    }

    $comments = $post->comments()->where('is_approved', true)->latest()->get();

    return view('blog-show', [
        'post' => $post,
        'comments' => $comments,
    ]);
})->name('blog.show');

Route::post('/blog/{slug}/like', function ($slug) {
    $post = Post::where('slug', $slug)->firstOrFail();

    $liked = session()->get('liked_posts', []);
    if (in_array($post->id, $liked)) {
        return response()->json(['success' => false, 'message' => 'Déjà liké']);
    }

    $post->increment('likes');
    session()->push('liked_posts', $post->id);

    return response()->json(['success' => true, 'likes' => $post->likes]);
})->name('blog.like');

Route::post('/blog/{slug}/comment', function (Request $request, $slug) {
    $post = Post::where('slug', $slug)->firstOrFail();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'content' => 'required|string|max:1000',
    ]);

    $validated['post_id'] = $post->id;
    $validated['is_approved'] = false; // Nécessite validation

    PostComment::create($validated);

    return response()->json(['success' => true]);
})->name('blog.comment');

Route::get('/temoignages', function () {
    return view('temoignages', [
        'testimonials' => Testimonial::where('is_approved', true)->latest()->get(),
    ]);
})->name('temoignages');

Route::post('/temoignages', function (Request $request) {
    $validated = $request->validate([
        'client_name' => 'required|string|max:255',
        'client_role' => 'nullable|string|max:255',
        'content' => 'required|string|max:1000',
        'rating' => 'required|integer|min:1|max:5',
    ]);

    // Générer des initiales basées sur le nom du client
    $words = explode(' ', $validated['client_name']);
    $initials = '';
    foreach ($words as $w) {
        $initials .= strtoupper(substr($w, 0, 1));
    }
    $validated['initials'] = substr($initials, 0, 3) ?: 'OMF';
    $validated['is_approved'] = false; // Nécessite modération

    Testimonial::create($validated);

    return response()->json(['success' => true]);
});
// =====================
// Demandes de Devis
// =====================
Route::get('/devis', [QuoteRequestController::class, 'create'])->name('devis');
Route::post('/devis', [QuoteRequestController::class, 'store'])->name('devis.store');

// =====================
// Newsletter
// =====================
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// =====================
// ChatBot IA
// =====================
Route::post('/chatbot', [ChatBotController::class, 'chat'])->name('chatbot');

// =====================
// Pages Légales
// =====================
Route::get('/mentions-legales', function () {
    return view('legal.mentions-legales');
})->name('mentions-legales');

Route::get('/politique-confidentialite', function () {
    return view('legal.politique-confidentialite');
})->name('politique-confidentialite');

Route::get('/conditions-utilisation', function () {
    return view('legal.conditions-utilisation');
})->name('conditions-utilisation');

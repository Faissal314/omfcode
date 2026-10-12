<x-layout>
  <section class="relative overflow-hidden hero-glow">
    <div class="absolute inset-0 grid-bg opacity-70"></div>
    <div class="relative max-w-5xl mx-auto px-5 lg:px-8 py-20 text-center">
      <span class="chip">Blog</span>
      <h1 class="font-display font-bold text-5xl md:text-6xl mt-4">Le journal de bord d'OMF.</h1>
      <p class="mt-6 text-lg text-ink-2 max-w-2xl mx-auto">Analyses, tutoriels, décryptages sur l'IA, le dev, la cybersécurité et les PME.</p>
    </div>
  </section>

  <section class="max-w-7xl mx-auto px-5 lg:px-8 py-12">
    @if($featured)
    <!-- Article à la une -->
    <a href="{{ route('blog.show', $featured->slug) }}" class="card overflow-hidden grid md:grid-cols-2 card-hover mb-10 reveal">
      <div class="aspect-video md:aspect-auto bg-gradient-to-br from-brand to-indigo-900">
        @if($featured->imagePublicUrl())
          <img src="{{ $featured->imagePublicUrl() }}" alt="{{ $featured->title }}" class="w-full h-full object-cover" />
        @endif
      </div>
      <div class="p-8">
        <p class="text-xs text-brand font-semibold">{{ strtoupper($featured->category) }} · À LA UNE</p>
        <h2 class="font-display font-bold text-3xl mt-2">{{ $featured->title }}</h2>
        <p class="text-ink-2 mt-3">{{ Str::limit(strip_tags($featured->content), 180) }}</p>
        <p class="text-xs text-muted mt-4">👁 {{ $featured->views }} · ❤ {{ $featured->likes }} · {{ $featured->read_time ?? '10 min' }} de lecture · {{ $featured->created_at->translatedFormat('F Y') }}</p>
      </div>
    </a>
    @endif

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      @forelse($posts as $post)
      <a href="{{ route('blog.show', $post->slug) }}" class="card overflow-hidden card-hover reveal">
        <div class="aspect-video bg-gradient-to-br from-brand/60 to-indigo-900">
          @if($post->imagePublicUrl())
            <img src="{{ $post->imagePublicUrl() }}" alt="{{ $post->title }}" class="w-full h-full object-cover" />
          @endif
        </div>
        <div class="p-5">
          <p class="text-xs text-brand font-semibold">{{ strtoupper($post->category) }}</p>
          <h3 class="font-display font-bold text-lg mt-1">{{ $post->title }}</h3>
          <p class="text-xs text-muted mt-3">👁 {{ $post->views }} · ❤ {{ $post->likes }} · {{ $post->read_time ?? '5 min' }} de lecture</p>
        </div>
      </a>
      @empty
      <div class="md:col-span-3 text-center py-16">
        <p class="text-4xl mb-4">✍️</p>
        <p class="font-display font-bold text-2xl text-ink">Articles en préparation !</p>
        <p class="text-muted mt-2">Notre équipe rédige des contenus de qualité. Inscrivez-vous à la newsletter pour être informé.</p>
      </div>
      @endforelse
    </div>
  </section>
</x-layout>

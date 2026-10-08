<x-layout>
  <section class="relative overflow-hidden hero-glow">
    <div class="absolute inset-0 grid-bg opacity-70"></div>
    <div class="relative max-w-5xl mx-auto px-5 lg:px-8 py-24 text-center">
      <span class="chip">Services</span>
      <h1 class="font-display font-bold text-5xl md:text-6xl mt-4">Une offre complète, sans compromis.</h1>
      <p class="mt-6 text-lg text-ink-2 max-w-2xl mx-auto">Du site vitrine au système IA, chaque service est livré par des spécialistes.</p>
    </div>
  </section>

  <section class="max-w-7xl mx-auto px-5 lg:px-8 py-16 space-y-16">
    @foreach($categories as $category)
    <div id="{{ $category->slug }}" class="grid lg:grid-cols-3 gap-8 items-start reveal" style="scroll-margin-top: 100px;">
      <div class="lg:col-span-1">
        <div class="w-12 h-12 rounded-xl bg-brand-050 text-brand grid place-items-center text-xl mb-4">{!! $category->icon !!}</div>
        <h2 class="font-display font-bold text-3xl">{{ $category->name }}</h2>
        <p class="mt-3 text-ink-2">{{ $category->description }}</p>
        <a href="#" class="btn btn-ghost mt-5">Explorer →</a>
      </div>
      <div class="lg:col-span-2 grid sm:grid-cols-2 gap-4">
        @foreach($category->services as $service)
        <a href="{{ route('services.show', $service->slug) }}" class="card p-5 card-hover">
          <h3 class="font-display font-bold">{{ $service->title }}</h3>
          <p class="text-sm text-muted mt-1">{{ $service->short_description }}</p>
        </a>
        @endforeach
      </div>
    </div>
    @endforeach
  </section>
</x-layout>

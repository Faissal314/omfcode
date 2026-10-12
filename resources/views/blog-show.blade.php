<x-layout>
  <section class="relative overflow-hidden hero-glow">
    <div class="absolute inset-0 grid-bg opacity-70"></div>
    <div class="relative max-w-4xl mx-auto px-5 lg:px-8 py-16 text-center">
      <a href="{{ route('blog') }}" class="chip hover:bg-brand-050 mb-4 inline-block">← Retour au blog</a>
      <span class="chip mt-1 block w-max mx-auto">{{ strtoupper($post->category) }}</span>
      <h1 class="font-display font-bold text-4xl md:text-6xl mt-4">{{ $post->title }}</h1>
      <p class="mt-4 text-sm text-muted">
        Publié le {{ $post->created_at->translatedFormat('d F Y') }} · 
        <span id="view-count">👁 {{ $post->views }} vues</span> · 
        {{ $post->read_time ?? '5 min' }} de lecture
      </p>
    </div>
  </section>

  <section class="max-w-4xl mx-auto px-5 lg:px-8 py-12">
    <!-- Article Image and Content -->
    <div class="card p-6 md:p-10 reveal">
      @if($post->imagePublicUrl())
        <div class="aspect-video w-full rounded-xl overflow-hidden mb-8">
          <img src="{{ $post->imagePublicUrl() }}" alt="{{ $post->title }}" class="w-full h-full object-cover" />
        </div>
      @endif

      <div class="prose prose-lg dark:prose-invert max-w-none">
        {!! $post->content !!}
      </div>

      <!-- Likes Section -->
      <div class="mt-12 pt-6 border-t border-line flex items-center justify-between">
        <div class="flex items-center gap-2">
          <button id="likeBtn" onclick="likePost()" class="btn btn-ghost border border-line flex items-center gap-2 transition-all">
            <span class="text-xl">❤️</span> 
            <span id="like-text">J'aime</span> 
            <span id="like-count" class="font-bold bg-brand-050 text-brand px-2 py-0.5 rounded-full text-xs">{{ $post->likes }}</span>
          </button>
        </div>
        <div class="text-xs text-muted">
          Partager cet article si vous l'avez apprécié !
        </div>
      </div>
    </div>

    <!-- Comments Section -->
    <div class="mt-12 reveal">
      <h2 class="font-display font-bold text-2xl mb-6">Commentaires ({{ $comments->count() }})</h2>

      <!-- Comment list -->
      <div class="space-y-4 mb-8">
        @forelse($comments as $comment)
          <div class="card p-5">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-brand-050 text-brand grid place-items-center font-bold text-sm">
                  {{ strtoupper(substr($comment->name, 0, 1)) }}
                </span>
                <span class="font-semibold text-sm">{{ $comment->name }}</span>
              </div>
              <span class="text-xs text-muted">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-ink-2 text-sm pl-10">{{ $comment->content }}</p>
          </div>
        @empty
          <div class="card p-6 text-center text-muted">
            <p class="text-sm">Aucun commentaire pour le moment. Soyez le premier à donner votre avis !</p>
          </div>
        @endforelse
      </div>

      <!-- Add Comment Form -->
      <div class="card p-6 md:p-8">
        <h3 class="font-display font-bold text-xl mb-4">Laisser un commentaire</h3>
        <form id="commentForm" onsubmit="submitComment(event)" class="grid gap-4">
          @csrf
          <div class="grid sm:grid-cols-2 gap-4">
            <label class="block">
              <span class="text-sm font-medium">Nom complet</span>
              <input name="name" required class="mt-1 w-full px-3 py-2 rounded-lg bg-app border border-line ring-brand" />
            </label>
            <label class="block">
              <span class="text-sm font-medium">Email (ne sera pas publié)</span>
              <input type="email" name="email" class="mt-1 w-full px-3 py-2 rounded-lg bg-app border border-line ring-brand" />
            </label>
          </div>
          <label class="block">
            <span class="text-sm font-medium">Votre commentaire</span>
            <textarea name="content" rows="4" required class="mt-1 w-full px-3 py-2 rounded-lg bg-app border border-line ring-brand"></textarea>
          </label>
          <button type="submit" id="commentBtn" class="btn btn-primary justify-center w-max">Publier le commentaire</button>
          <p id="commentOk" class="hidden text-brand text-sm">✓ Commentaire soumis ! Il sera visible après modération de l'administrateur.</p>
        </form>
      </div>
    </div>
  </section>

  <script>
    async function likePost() {
      const btn = document.getElementById('likeBtn');
      try {
        const response = await fetch('{{ route("blog.like", $post->slug) }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        });
        const res = await response.json();
        if (res.success) {
          document.getElementById('like-count').innerText = res.likes;
          btn.classList.add('bg-brand-050', 'text-brand');
          document.getElementById('like-text').innerText = 'Aimé !';
        } else {
          alert(res.message || "Erreur lors du like.");
        }
      } catch (err) {
        alert("Erreur lors du like.");
      }
    }

    async function submitComment(e) {
      e.preventDefault();
      const form = e.target;
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      const btn = document.getElementById('commentBtn');
      const oldText = btn.innerText;
      btn.innerText = 'Envoi...';
      btn.disabled = true;

      try {
        const response = await fetch('{{ route("blog.comment", $post->slug) }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify(data)
        });

        if (response.ok) {
          form.reset();
          document.getElementById('commentOk').classList.remove('hidden');
        } else {
          alert("Une erreur s'est produite lors de l'envoi du commentaire.");
        }
      } catch (error) {
        alert("Une erreur s'est produite lors de l'envoi du commentaire.");
      } finally {
        btn.innerText = oldText;
        btn.disabled = false;
      }
    }
  </script>
</x-layout>

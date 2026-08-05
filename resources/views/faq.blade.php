<x-layout>
  <section class="relative overflow-hidden hero-glow">
    <div class="absolute inset-0 grid-bg opacity-70"></div>
    <div class="relative max-w-4xl mx-auto px-5 lg:px-8 py-20 text-center">
      <span class="chip">FAQ</span>
      <h1 class="font-display font-bold text-5xl md:text-6xl mt-4">Questions fréquentes.</h1>
      <p class="mt-6 text-lg text-ink-2">Consultez les questions déjà posées par nos visiteurs et posez la vôtre directement au chatbot.</p>
    </div>
  </section>

  <section class="max-w-4xl mx-auto px-5 lg:px-8 py-12 grid gap-10 lg:grid-cols-[1.2fr_0.8fr]">
    <div class="space-y-6">
      <div class="card p-6 reveal">
        <div class="flex items-start justify-between gap-4">
          <div>
            <p class="font-display text-sm uppercase tracking-[0.24em] text-brand">Interaction publique</p>
            <h2 class="font-display font-bold text-2xl mt-3">Toutes les questions sont visibles par tous.</h2>
            <p class="mt-3 text-sm text-ink-2">Votre question sera traitée par l'assistant IA et ajoutée à la FAQ publique pour aider les prochains visiteurs.</p>
          </div>
        </div>

        <form id="faqAskForm" class="mt-8 space-y-4">
          <div>
            <label for="faqQuestion" class="block text-sm font-medium text-ink">Posez votre question</label>
            <textarea id="faqQuestion" name="question" rows="4" required maxlength="500" class="mt-2 w-full rounded-2xl border border-line bg-app px-4 py-3 text-sm text-ink outline-none focus:border-brand focus:ring-brand/20"></textarea>
          </div>
          <button type="submit" class="btn btn-primary w-full">Envoyer au chatbot</button>
          <p id="faqAskMessage" class="text-sm text-muted hidden"></p>
        </form>
      </div>

      <div class="card p-6 reveal">
        <h3 class="font-display font-bold text-xl">Astuce</h3>
        <p class="mt-3 text-sm text-ink-2">Vous pouvez aussi échanger avec notre assistant IA en bas à droite de cette page. Toute conversation est guidée par le même moteur que la FAQ.</p>
        <div class="mt-4 grid gap-3">
          <a href="javascript:window.omfChatToggle()" class="btn btn-outline">Ouvrir le chatbot</a>
          <a href="{{ route('contact') }}" class="btn btn-secondary">Nous contacter</a>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <div class="card p-6 reveal">
        <div class="flex items-center justify-between gap-4">
          <div>
            <p class="font-display text-sm uppercase tracking-[0.24em] text-brand">FAQ publique</p>
            <h2 class="font-display font-bold text-2xl mt-3">Questions récemment posées</h2>
          </div>
          <span class="rounded-full bg-brand/10 px-3 py-1 text-xs font-semibold text-brand">Visible par tous</span>
        </div>

        <div id="faqList" class="mt-6 space-y-4">
          @foreach($faqQuestions as $faq)
            <details class="group border border-line rounded-3xl bg-app p-5" open>
              <summary class="cursor-pointer font-display font-semibold text-base flex justify-between items-center gap-3">
                <span>{{ $faq->question }}</span>
                <span class="transition group-open:rotate-45 text-brand text-2xl leading-none">+</span>
              </summary>
              <p class="mt-4 text-sm text-ink-2 leading-7">{!! nl2br(e($faq->answer)) !!}</p>
              <p class="mt-4 text-xs text-muted">Source : {{ ucfirst($faq->source) }}</p>
            </details>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <section class="max-w-4xl mx-auto px-5 lg:px-8 pb-20 text-center">
    <div class="card p-8 reveal">
      <p class="font-display font-bold text-xl">Toujours besoin d'aide ?</p>
      <p class="text-muted text-sm mt-2">Le chatbot IA et notre équipe restent disponibles pour répondre à vos besoins.</p>
      <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
        <a href="javascript:window.omfChatToggle()" class="btn btn-primary">Parler au chatbot</a>
        <a href="{{ route('contact') }}" class="btn btn-outline">Contact direct</a>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('faqAskForm');
      const message = document.getElementById('faqAskMessage');
      const list = document.getElementById('faqList');

      form?.addEventListener('submit', async function (event) {
        event.preventDefault();
        const textarea = document.getElementById('faqQuestion');
        if (!textarea) return;

        const question = textarea.value.trim();
        if (!question) return;

        message.classList.add('text-brand');
        message.classList.remove('text-red-500');
        message.textContent = 'Envoi en cours...';
        message.classList.remove('hidden');

        try {
          const response = await fetch('{{ route('faq.ask') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ question })
          });

          const data = await response.json();
          if (!data.success) {
            throw new Error(data.message || 'Impossible de récupérer la réponse.');
          }

          const faqItem = document.createElement('details');
          faqItem.className = 'group border border-line rounded-3xl bg-app p-5';
          faqItem.open = true;
          faqItem.innerHTML = `
            <summary class="cursor-pointer font-display font-semibold text-base flex justify-between items-center gap-3">
              <span>${question}</span>
              <span class="transition group-open:rotate-45 text-brand text-2xl leading-none">+</span>
            </summary>
            <p class="mt-4 text-sm text-ink-2 leading-7">${data.answer.replace(/\n/g, '<br>')}</p>
            <p class="mt-4 text-xs text-muted">Source : IA</p>
          `;

          list.prepend(faqItem);
          textarea.value = '';
          message.textContent = 'Merci ! Votre question a été ajoutée à la FAQ publique.';
        } catch (error) {
          message.textContent = 'Une erreur est survenue. Veuillez réessayer.';
          message.classList.add('text-red-500');
        }
      });
    });
  </script>
</x-layout>

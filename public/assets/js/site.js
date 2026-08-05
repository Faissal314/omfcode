/* OMF Code — shared site JS: theme toggle, header/footer injection, reveal, mobile nav. */

(function () {
  // Theme
  const saved = localStorage.getItem('omf-theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const initialDark = saved ? saved === 'dark' : prefersDark;
  document.documentElement.classList.toggle('dark', initialDark);

  window.toggleTheme = function () {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('omf-theme', isDark ? 'dark' : 'light');
    document.querySelectorAll('[data-theme-icon]').forEach(el => {
      el.textContent = isDark ? '☀' : '☾';
    });
  };
})();

const NAV = [
  { label: 'Accueil', href: '/' },
  { label: 'À propos', href: '/about' },
  {
    label: 'Services', href: '/services',
    children: [
      { label: 'Développement', href: '/services' },
      { label: 'Intelligence artificielle', href: '/services' },
      { label: 'Formation', href: '/services' },
      { label: 'Conseil', href: '/services' },
    ],
  },
  { label: 'Réalisations', href: '/realisations' },
  { label: 'Blog', href: '/blog' },
  { label: 'Témoignages', href: '/temoignages' },
  { label: 'FAQ', href: '/faq' },
  { label: 'Contact', href: '/contact' },
];

function headerHTML() {
  const items = NAV.map(n => {
    if (n.children) {
      return `
        <div class="relative group">
          <a href="${n.href}" class="px-3 py-2 text-sm font-medium text-ink hover:text-brand transition inline-flex items-center gap-1">
            ${n.label}
            <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path d="M5 8l5 5 5-5H5z"/></svg>
          </a>
          <div class="absolute left-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition min-w-[240px] z-50">
            <div class="card p-2 shadow-2xl">
              ${n.children.map(c => `<a href="${c.href}" class="block px-3 py-2 rounded-lg text-sm text-ink-2 hover:bg-app-2 hover:text-brand">${c.label}</a>`).join('')}
            </div>
          </div>
        </div>`;
    }
    return `<a href="${n.href}" class="px-3 py-2 text-sm font-medium text-ink hover:text-brand transition">${n.label}</a>`;
  }).join('');

  return `
  <header class="sticky top-0 z-40 backdrop-blur-lg bg-app/80 border-b border-line" style="padding-top:env(safe-area-inset-top);">
    <div class="max-w-7xl mx-auto px-4 sm:px-5 lg:px-8 h-14 sm:h-16 flex items-center justify-between gap-3">
      <a href="/" class="flex min-w-0 items-center shrink-0">
        <img src="/assets/img/logo.png" alt="OMF code" class="h-12 sm:h-14 w-auto" />
      </a>
      
      <!-- Menu Desktop -->
      <nav class="hidden lg:flex items-center gap-2">
        <div class="flex items-center mr-4">${items}</div>
        <button onclick="toggleTheme()" aria-label="Basculer le thème" class="w-9 h-9 grid place-items-center rounded-lg border border-line hover:bg-app-2 transition">
          <span data-theme-icon>${document.documentElement.classList.contains('dark') ? '☀' : '☾'}</span>
        </button>
        <a href="/faq" class="btn btn-outline text-sm ml-2 hidden lg:inline-flex">FAQ</a>
        <a href="/rendez-vous" class="btn btn-primary text-sm ml-2">Prendre rendez-vous</a>
      </nav>

      <!-- Hamburger (Mobile uniquement) -->
      <div class="flex items-center lg:hidden shrink-0">
        <button id="mobileNavToggle" aria-controls="mobileNav" aria-expanded="false" onclick="toggleMobileNav()" class="w-10 h-10 grid place-items-center rounded-lg border border-line" aria-label="Menu">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </button>
      </div>
    </div>
    
    <!-- Menu Mobile -->
    <div id="mobileNav" class="hidden lg:hidden border-t border-line bg-app/95 backdrop-blur-lg overflow-y-auto overscroll-contain" style="max-height:calc(100vh - 3.5rem - env(safe-area-inset-top));">
      <div class="max-w-7xl mx-auto px-5 py-3 flex flex-col pb-[calc(1rem+env(safe-area-inset-bottom))]">
        ${NAV.map(n => `<a href="${n.href}" class="py-3 text-ink hover:text-brand border-b border-line/40 last:border-0">${n.label}</a>${n.children ? n.children.map(c => `<a href="${c.href}" class="py-2 pl-4 text-sm text-muted hover:text-brand">— ${c.label}</a>`).join('') : ''}`).join('')}
        
        <div class="mt-6 flex items-center justify-between border-t border-line/40 pt-4">
            <span class="text-ink text-sm font-medium">Thème visuel</span>
            <button onclick="toggleTheme()" aria-label="Basculer le thème" class="w-10 h-10 grid place-items-center rounded-lg border border-line hover:bg-app-2 transition">
            <span data-theme-icon>${document.documentElement.classList.contains('dark') ? '☀' : '☾'}</span>
            </button>
        </div>
        <a href="/rendez-vous" class="btn btn-primary mt-4 justify-center">Prendre rendez-vous</a>
      </div>
    </div>
  </header>`;
}

window.toggleMobileNav = function () {
  const nav = document.getElementById('mobileNav');
  const btn = document.getElementById('mobileNavToggle');
  if (!nav) return;
  const open = nav.classList.toggle('hidden') === false;
  if (btn) btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  document.body.style.overflow = open ? 'hidden' : '';
};

function footerHTML() {
  const y = new Date().getFullYear();
  return `
  <footer class="mt-24 border-t border-line bg-app-2">
    <div class="max-w-7xl mx-auto px-5 lg:px-8 py-16 grid gap-10 md:grid-cols-2 lg:grid-cols-4">
      <div>
        <div class="flex items-center gap-2 mb-4">
          <img src="/assets/img/logo.png" alt="OMF code" class="h-14 w-auto" />
        </div>
        <p class="text-sm text-muted max-w-xs">Studio de développement, IA & formation basé à Ouagadougou, Burkina Faso. Nous construisons les outils numériques dont les PME africaines ont besoin.</p>
        <div class="flex gap-2 mt-5">
          ${['LinkedIn', 'Twitter', 'Instagram', 'Facebook', 'YouTube'].map(n => `<a href="#" aria-label="${n}" class="w-9 h-9 grid place-items-center rounded-lg border border-line hover:border-brand hover:text-brand transition">${n[0]}</a>`).join('')}
        </div>
      </div>
      <div>
        <h4 class="font-display font-bold text-ink mb-4">Services</h4>
        <ul class="space-y-2 text-sm text-muted">
          <li><a class="hover:text-brand" href="/site/services/developpement.html">Développement</a></li>
          <li><a class="hover:text-brand" href="/site/services/intelligence-artificielle.html">Intelligence artificielle</a></li>
          <li><a class="hover:text-brand" href="/site/services/formation.html">Formation</a></li>
          <li><a class="hover:text-brand" href="/site/services/conseil.html">Conseil</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-display font-bold text-ink mb-4">Entreprise</h4>
        <ul class="space-y-2 text-sm text-muted">
          <li><a class="hover:text-brand" href="/about">Qui sommes-nous</a></li>
          <li><a class="hover:text-brand" href="#">Réalisations</a></li>
          <li><a class="hover:text-brand" href="#">Blog</a></li>
          <li><a class="hover:text-brand" href="#">Témoignages</a></li>
          <li><a class="hover:text-brand" href="/faq">FAQ</a></li>
          <li><a class="hover:text-brand" href="/contact">Contact</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-display font-bold text-ink mb-4">Newsletter</h4>
        <p class="text-sm text-muted mb-3">Recevez nos analyses IA & tech chaque semaine.</p>
        <form class="flex gap-2" onsubmit="window.omfSubscribeNewsletter(event, this)">
          <input type="email" name="email" required placeholder="vous@exemple.com" class="flex-1 px-3 py-2 rounded-lg bg-app border border-line text-sm ring-brand text-ink" />
          <button type="submit" class="btn btn-primary text-sm">OK</button>
        </form>
        <p class="newsletter-msg hidden text-xs mt-2"></p>
      </div>
    </div>
    <div class="border-t border-line">
      <div class="max-w-7xl mx-auto px-5 lg:px-8 py-6 flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-muted">
        <p>© ${y} OMF &lt;code/&gt;. Tous droits réservés.</p>
        <div class="flex gap-5">
          <a class="hover:text-brand" href="/mentions-legales">Mentions légales</a>
          <a class="hover:text-brand" href="/politique-confidentialite">Confidentialité</a>
          <a class="hover:text-brand" href="/conditions-utilisation">CGU</a>
        </div>
      </div>
    </div>
  </footer>`;
}

function chatbotHTML() {
  return `
  <style>
    /* === Liquid Glass Chatbot === */
    #omfChatBtn {
      position: relative;
      background: linear-gradient(135deg, #7C3AED, #06B6D4);
      border: none;
      border-radius: 50%;
      width: 56px;
      height: 56px;
      color: white;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 32px rgba(124,58,237,0.45), 0 0 0 1px rgba(255,255,255,0.12) inset;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      backdrop-filter: blur(10px);
    }
    #omfChatBtn::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 50%;
      background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0) 60%);
      pointer-events: none;
    }
    #omfChatBtn:hover { transform: translateY(-3px) scale(1.07); box-shadow: 0 12px 40px rgba(124,58,237,0.6), 0 0 0 1px rgba(255,255,255,0.18) inset; }
    /* Pulse ring around button */
    #omfChatBtn::after {
      content: '';
      position: absolute;
      inset: -4px;
      border-radius: 50%;
      border: 2px solid rgba(124,58,237,0.4);
      animation: ring-pulse 2.5s ease-out infinite;
      pointer-events: none;
    }
    @keyframes ring-pulse {
      0% { opacity: 1; transform: scale(1); }
      100% { opacity: 0; transform: scale(1.5); }
    }
    @keyframes pulse-dot {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.6; transform: scale(1.3); }
    }

    /* Glass panel */
    #omfChatPanel {
      position: absolute;
      bottom: 72px;
      right: 0;
      width: 360px;
      border-radius: 24px;
      overflow: hidden;
      box-shadow:
        0 32px 64px rgba(0,0,0,0.4),
        0 0 0 1px rgba(255,255,255,0.10) inset,
        0 1px 0 rgba(255,255,255,0.15) inset;
      background: rgba(15, 15, 25, 0.72);
      backdrop-filter: blur(32px) saturate(1.8);
      -webkit-backdrop-filter: blur(32px) saturate(1.8);
      border: 1px solid rgba(255,255,255,0.08);
      transform-origin: bottom right;
      transition: opacity 0.25s ease, transform 0.25s ease;
    }
    #omfChatPanel.hidden {
      opacity: 0;
      transform: scale(0.92) translateY(10px);
      pointer-events: none;
    }
    #omfChatPanel:not(.hidden) {
      opacity: 1;
      transform: scale(1) translateY(0);
    }

    /* Panel header */
    #omfChatPanel .chat-header {
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: linear-gradient(135deg, rgba(124,58,237,0.4) 0%, rgba(6,182,212,0.2) 100%);
      border-bottom: 1px solid rgba(255,255,255,0.08);
      position: relative;
    }
    #omfChatPanel .chat-header::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(255,255,255,0.06) 0%, transparent 60%);
      pointer-events: none;
    }
    #omfChatPanel .chat-header-info { display: flex; align-items: center; gap: 10px; }
    #omfChatPanel .chat-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: linear-gradient(135deg, #7C3AED, #06B6D4);
      display: grid; place-items: center; font-size: 16px;
      box-shadow: 0 0 0 2px rgba(255,255,255,0.15);
    }
    #omfChatPanel .chat-header-title { color: white; font-weight: 700; font-size: 14px; font-family: 'Space Grotesk', sans-serif; }
    #omfChatPanel .chat-header-sub { color: rgba(255,255,255,0.6); font-size: 11px; display: flex; align-items: center; gap: 4px; }
    #omfChatPanel .chat-online-dot {
      width: 6px; height: 6px; border-radius: 50%;
      background: #4ade80; box-shadow: 0 0 6px #4ade80;
      animation: pulse-dot 2s infinite;
    }
    #omfChatCloseBtn {
      width: 28px; height: 28px; border-radius: 50%;
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.15);
      color: rgba(255,255,255,0.7);
      cursor: pointer; display: grid; place-items: center;
      font-size: 14px; transition: all 0.15s ease;
      position: relative; z-index: 1;
    }
    #omfChatCloseBtn:hover { background: rgba(255,80,80,0.3); color: white; border-color: rgba(255,80,80,0.4); }

    /* Messages */
    #omfChatLog {
      height: 280px;
      overflow-y: auto;
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      scrollbar-width: thin;
      scrollbar-color: rgba(124,58,237,0.3) transparent;
    }
    #omfChatLog::-webkit-scrollbar { width: 4px; }
    #omfChatLog::-webkit-scrollbar-track { background: transparent; }
    #omfChatLog::-webkit-scrollbar-thumb { background: rgba(124,58,237,0.4); border-radius: 4px; }
    #omfChatLog .msg-bot {
      background: rgba(255,255,255,0.07);
      border: 1px solid rgba(255,255,255,0.08);
      color: rgba(255,255,255,0.88);
      border-radius: 18px 18px 18px 4px;
      padding: 10px 14px;
      font-size: 13px;
      line-height: 1.5;
      max-width: 85%;
      align-self: flex-start;
    }
    #omfChatLog .msg-user {
      background: linear-gradient(135deg, rgba(124,58,237,0.6), rgba(6,182,212,0.4));
      border: 1px solid rgba(124,58,237,0.3);
      color: white;
      border-radius: 18px 18px 4px 18px;
      padding: 10px 14px;
      font-size: 13px;
      line-height: 1.5;
      max-width: 85%;
      align-self: flex-end;
      backdrop-filter: blur(8px);
    }
    .msg-typing { display: flex; gap: 4px; align-items: center; padding: 10px 14px; }
    .msg-typing span {
      width: 7px; height: 7px; background: rgba(124,58,237,0.8);
      border-radius: 50%; animation: typing-bounce 1.2s infinite;
    }
    .msg-typing span:nth-child(2) { animation-delay: 0.2s; }
    .msg-typing span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing-bounce {
      0%, 60%, 100% { transform: translateY(0); }
      30% { transform: translateY(-6px); }
    }

    /* Input area */
    #omfChatForm {
      padding: 12px 16px;
      border-top: 1px solid rgba(255,255,255,0.07);
      display: flex;
      gap: 8px;
      background: rgba(0,0,0,0.2);
    }
    #omfChatInput {
      flex: 1;
      background: rgba(255,255,255,0.07);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 12px;
      padding: 10px 14px;
      color: white;
      font-size: 13px;
      outline: none;
      transition: border-color 0.2s;
    }
    #omfChatInput::placeholder { color: rgba(255,255,255,0.35); }
    #omfChatInput:focus { border-color: rgba(124,58,237,0.6); background: rgba(255,255,255,0.09); }
    #omfChatSendBtn {
      width: 38px; height: 38px; border-radius: 12px;
      background: linear-gradient(135deg, #7C3AED, #06B6D4);
      border: none; color: white; cursor: pointer;
      display: grid; place-items: center;
      transition: transform 0.15s, opacity 0.15s;
      flex-shrink: 0;
    }
    #omfChatSendBtn:hover { transform: scale(1.08); }
    #omfChatSendBtn:disabled { opacity: 0.4; transform: none; cursor: default; }

    /* Responsive */
    @media (max-width: 420px) {
      #omfChatPanel { width: calc(100vw - 24px); right: -4px; }
    }
  </style>

  <div id="omfChat" style="position:fixed;bottom:20px;right:20px;z-index:9999;">
    <button id="omfChatBtn" onclick="window.omfChatToggle()" title="Assistant IA OMF">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><circle cx="9" cy="10" r="1" fill="currentColor"/><circle cx="12" cy="10" r="1" fill="currentColor"/><circle cx="15" cy="10" r="1" fill="currentColor"/></svg>
    </button>

    <div id="omfChatPanel" class="hidden">
      <div class="chat-header">
        <div class="chat-header-info">
          <div class="chat-avatar">🤖</div>
          <div>
            <div class="chat-header-title">Assistant OMF</div>
            <div class="chat-header-sub">
              <span class="chat-online-dot"></span>
              En ligne · Propulsé par IA
            </div>
          </div>
        </div>
        <button id="omfChatCloseBtn" onclick="window.omfChatToggle()" title="Fermer">✕</button>
      </div>

      <div id="omfChatLog">
        <div class="msg-bot">Bonjour 👋 Je suis l'assistant IA d'OMF. Je peux vous renseigner sur nos services de développement, IA et formation. Comment puis-je vous aider ?</div>
        <div id="omfChatSuggestions" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">
          ${[
      [' Nos services', 'Quels sont vos services ?'],
      [' Nous contacter', 'Comment vous contacter ?'],
      [' Demander un devis', 'Je veux demander un devis'],
      [' Nos solutions', 'Quelles solutions proposez-vous ?'],
    ].map(([label, msg]) => `<button onclick="window.omfChatQuick('${msg.replace(/'/g, "\\'")}')"
            style="background:rgba(124,58,237,0.15);border:1px solid rgba(124,58,237,0.3);color:rgba(255,255,255,0.8);border-radius:20px;padding:5px 11px;font-size:11.5px;cursor:pointer;transition:all 0.15s;font-family:inherit;"
            onmouseover="this.style.background='rgba(124,58,237,0.35)';this.style.color='white'"
            onmouseout="this.style.background='rgba(124,58,237,0.15)';this.style.color='rgba(255,255,255,0.8)'">${label}</button>`).join('')}
        </div>
      </div>

      <form id="omfChatForm" onsubmit="omfChatSend(event)">
        <input id="omfChatInput" placeholder="Posez votre question…" autocomplete="off"/>
        <button type="submit" id="omfChatSendBtn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
      </form>
    </div>
  </div>`;
}

window.omfChatHistory = [];

window.omfChatToggle = function () {
  const panel = document.getElementById('omfChatPanel');
  if (!panel) return;
  panel.classList.toggle('hidden');
  if (!panel.classList.contains('hidden')) {
    document.getElementById('omfChatInput')?.focus();
  }
};

// Quick suggestion chips: hide them after first use and trigger the message
window.omfChatQuick = function (msg) {
  const suggestions = document.getElementById('omfChatSuggestions');
  if (suggestions) suggestions.style.display = 'none';
  const input = document.getElementById('omfChatInput');
  if (input) {
    input.value = msg;
    const form = document.getElementById('omfChatForm');
    if (form) form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
  }
};

window.omfChatSend = async function (e) {
  e.preventDefault();
  const input = document.getElementById('omfChatInput');
  const log = document.getElementById('omfChatLog');
  const btn = document.getElementById('omfChatSendBtn');
  const val = input.value.trim();
  if (!val) return;

  // Affiche le message de l'utilisateur
  log.insertAdjacentHTML('beforeend', `<div class="msg-user">${val}</div>`);
  input.value = '';
  log.scrollTop = log.scrollHeight;

  if (btn) btn.disabled = true;

  // Indicateur de frappe animé
  const typingId = 'typing-' + Date.now();
  log.insertAdjacentHTML('beforeend', `<div id="${typingId}" class="msg-bot msg-typing"><span></span><span></span><span></span></div>`);
  log.scrollTop = log.scrollHeight;

  try {
    const response = await fetch('/chatbot', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({ message: val, history: window.omfChatHistory })
    });

    const data = await response.json();

    // Supprime l'indicateur
    document.getElementById(typingId)?.remove();

    // Ajout à l'historique
    window.omfChatHistory.push({ role: 'user', content: val });
    window.omfChatHistory.push({ role: 'assistant', content: data.reply });

    // Convert Markdown links [text](url) to HTML <a href="url">text</a>
    let replyHtml = data.reply.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" style="color:#06B6D4;text-decoration:underline">$1</a>');
    // Convert newlines to <br>
    replyHtml = replyHtml.replace(/\n/g, '<br>');

    log.insertAdjacentHTML('beforeend', `<div class="msg-bot">${replyHtml}</div>`);
  } catch (error) {
    document.getElementById(typingId)?.remove();
    log.insertAdjacentHTML('beforeend', `<div class="msg-bot" style="color:#f87171">Erreur de connexion. Veuillez réessayer.</div>`);
  }

  if (btn) btn.disabled = false;
  log.scrollTop = log.scrollHeight;
};

window.omfSubscribeNewsletter = async function (e, form) {
  e.preventDefault();
  const msgEl = form.parentElement.querySelector('.newsletter-msg');
  const email = form.querySelector('input').value;
  const btn = form.querySelector('button');

  btn.disabled = true;

  try {
    const response = await fetch('/newsletter/subscribe', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({ email })
    });

    const data = await response.json();
    msgEl.textContent = data.message;
    msgEl.className = 'newsletter-msg text-xs mt-2 ' + (data.success ? 'text-green-500' : 'text-red-500');
    msgEl.classList.remove('hidden');

    if (data.success) {
      form.querySelector('input').value = '';
    }
  } catch (error) {
    msgEl.textContent = 'Erreur lors de l\'inscription.';
    msgEl.className = 'newsletter-msg text-xs mt-2 text-red-500';
    msgEl.classList.remove('hidden');
  }

  btn.disabled = false;
};

function scrollTopHTML() {
  return `
  <button id="omfScrollTop" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Retour en haut"
    class="fixed bottom-24 right-5 z-40 w-11 h-11 grid place-items-center rounded-full bg-brand text-white shadow-2xl opacity-0 pointer-events-none translate-y-2 transition-all hover:scale-110">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 15l6-6 6 6"/></svg>
  </button>`;
}

document.addEventListener('DOMContentLoaded', () => {
  const h = document.getElementById('siteHeader');
  const f = document.getElementById('siteFooter');
  if (h) h.innerHTML = headerHTML();
  if (f) f.innerHTML = footerHTML();
  document.body.insertAdjacentHTML('beforeend', chatbotHTML());
  document.body.insertAdjacentHTML('beforeend', scrollTopHTML());
  const stBtn = document.getElementById('omfScrollTop');
  const onScrollTop = () => {
    const show = window.scrollY > 400;
    stBtn.classList.toggle('opacity-0', !show);
    stBtn.classList.toggle('pointer-events-none', !show);
    stBtn.classList.toggle('translate-y-2', !show);
  };
  window.addEventListener('scroll', onScrollTop, { passive: true });
  onScrollTop();

  // Highlight active nav link
  const path = location.pathname;
  document.querySelectorAll('#siteHeader a').forEach(a => {
    if (a.getAttribute('href') === path) a.classList.add('text-brand');
  });

  // Reveal on scroll (all variants)
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
  }, { threshold: 0.12 });
  document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-blur').forEach(el => io.observe(el));

  // Auto-apply reveal to common blocks that don't have it (progressive enhancement)
  document.querySelectorAll('main section .card, main section h1, main section h2, main section > div > .grid > *').forEach(el => {
    if (!el.classList.contains('reveal') && !el.classList.contains('reveal-scale') && !el.classList.contains('reveal-blur') && !el.classList.contains('reveal-left') && !el.classList.contains('reveal-right')) {
      el.classList.add('reveal');
      io.observe(el);
    }
  });

  // Radial spotlight on cards (mouse position → CSS vars)
  document.addEventListener('mousemove', (e) => {
    const card = e.target.closest && e.target.closest('.card-hover');
    if (!card) return;
    const r = card.getBoundingClientRect();
    card.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%');
    card.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100) + '%');
  });

  // Tilt effect for [data-tilt]
  document.querySelectorAll('[data-tilt]').forEach(el => {
    el.addEventListener('mousemove', (e) => {
      const r = el.getBoundingClientRect();
      const px = (e.clientX - r.left) / r.width - 0.5;
      const py = (e.clientY - r.top) / r.height - 0.5;
      el.style.setProperty('--rx', (px * 10).toFixed(2) + 'deg');
      el.style.setProperty('--ry', (-py * 10).toFixed(2) + 'deg');
    });
    el.addEventListener('mouseleave', () => {
      el.style.setProperty('--rx', '0deg');
      el.style.setProperty('--ry', '0deg');
    });
  });

  // Section spotlight follow
  document.querySelectorAll('.spotlight').forEach(sec => {
    sec.addEventListener('mousemove', (e) => {
      const r = sec.getBoundingClientRect();
      sec.style.setProperty('--sx', ((e.clientX - r.left) / r.width * 100) + '%');
      sec.style.setProperty('--sy', ((e.clientY - r.top) / r.height * 100) + '%');
    });
  });

  // Animated counters — <span class="counter" data-count="500" data-prefix="+" data-suffix="">0</span>
  const cIO = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const el = e.target;
      const target = parseFloat(el.dataset.count || '0');
      const prefix = el.dataset.prefix || '';
      const suffix = el.dataset.suffix || '';
      const dur = 1400; const start = performance.now();
      function step(t) {
        const p = Math.min(1, (t - start) / dur);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = prefix + Math.round(target * eased).toLocaleString('fr-FR') + suffix;
        if (p < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
      cIO.unobserve(el);
    });
  }, { threshold: 0.5 });
  document.querySelectorAll('.counter[data-count]').forEach(el => cIO.observe(el));

  // Header shadow on scroll
  const header = document.querySelector('#siteHeader > header');
  if (header) {
    const onScroll = () => header.classList.toggle('shadow-lg', window.scrollY > 20);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // Scroll progress bar
  const bar = document.createElement('div');
  bar.style.cssText = 'position:fixed;top:0;left:0;height:3px;width:0%;background:linear-gradient(90deg,var(--brand),#7C3AED,#06B6D4);z-index:60;transition:width .1s linear;box-shadow:0 0 12px var(--ring);';
  document.body.appendChild(bar);
  window.addEventListener('scroll', () => {
    const h = document.documentElement;
    const pct = (h.scrollTop / (h.scrollHeight - h.clientHeight)) * 100;
    bar.style.width = pct + '%';
  }, { passive: true });
});

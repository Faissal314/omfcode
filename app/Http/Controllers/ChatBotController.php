<?php

namespace App\Http\Controllers;

use App\Models\FaqQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotController extends Controller
{
    // Prompt système qui donne son contexte à l'IA
    private string $systemPrompt = <<<'PROMPT'
Tu es l'assistant IA officiel de OMF <code/> (Ousmane Martial & Faissal), une agence de solutions numériques basée à Ouagadougou, Burkina Faso.
Tu dois répondre UNIQUEMENT dans le contexte de l'agence OMF et de ses services.
Sois chaleureux, professionnel et concis (3-5 phrases max sauf si on demande des détails).

SERVICES DISPONIBLES :
- Développement web (sites vitrine, e-commerce, applications web)
- Développement mobile (iOS, Android)
- Design & Identité visuelle (logos, chartes graphiques, UI/UX)
- Intelligence Artificielle & Automatisation (chatbots, agents IA, workflows)
- Conseil & Audit numérique
- Formation digitale & Prompt Engineering

RÈGLES ABSOLUES :
1. Prix/tarif/coût → Dis que les tarifs varient selon le projet et propose de [demander un devis](/devis)
2. Question hors-sujet → Invite à [envoyer un message](/contact)
3. Rendez-vous → Lien : [Prendre rendez-vous](/rendez-vous)
4. Réalisations → Lien : [Voir nos réalisations](/realisations)
5. Blog → Lien : [Lire notre blog](/blog)
6. TOUJOURS répondre en français. Ne jamais citer de concurrents.
7. Ne jamais inventer de prix ou de délais précis.
PROMPT;

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $userMessage = trim($request->input('message'));
        $history = $request->input('history', []);

        $apiKey = config('services.groq.key') ?: env('GROQ_API_KEY');

        // Si aucune clé API n'est configurée, utiliser le moteur de réponses intelligentes de secours
        if (empty($apiKey)) {
            $reply = $this->getSmartFallbackResponse($userMessage);

            return response()->json(['reply' => $reply]);
        }

        try {
            // Construire la liste des messages
            $messages = [
                ['role' => 'system', 'content' => $this->systemPrompt],
            ];

            // Historique récents (10 derniers échanges)
            $recentHistory = array_slice($history, -10);
            foreach ($recentHistory as $msg) {
                if (isset($msg['role'], $msg['content'])) {
                    $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
                }
            }

            // Message utilisateur
            $messages[] = ['role' => 'user', 'content' => $userMessage];

            $models = array_filter([
                config('services.groq.model'),
                'llama-3.3-70b-versatile',
                'llama-3.1-8b-instant',
                'gemma2-9b-it',
            ]);

            $reply = null;

            foreach ($models as $model) {
                $response = Http::withToken($apiKey)
                    ->timeout(12)
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => $model,
                        'messages' => $messages,
                        'max_tokens' => 450,
                        'temperature' => 0.6,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $content = $data['choices'][0]['message']['content'] ?? null;

                    if ($content) {
                        $content = preg_replace('/<think>.*?<\/think>/s', '', $content);
                        $reply = trim($content);
                        break;
                    }
                } else {
                    Log::warning("Groq model {$model} failed: ".$response->body());
                }
            }

            if (! $reply) {
                $reply = $this->getSmartFallbackResponse($userMessage);
            }
        } catch (\Exception $e) {
            Log::error('Groq API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $reply = $this->getSmartFallbackResponse($userMessage);
        }

        return response()->json(['reply' => $reply]);
    }

    /**
     * Moteur de réponse intelligente hors-ligne / de secours.
     */
    private function getSmartFallbackResponse(string $query): string
    {
        $normalized = mb_strtolower($query);

        // 1. Recherche dans la base de données FAQ de l'application
        try {
            $matchedFaq = FaqQuestion::where('is_public', true)
                ->whereNotNull('answer')
                ->where(function ($q) use ($normalized) {
                    $q->where('question', 'LIKE', "%{$normalized}%")
                        ->orWhere('answer', 'LIKE', "%{$normalized}%");
                })
                ->first();

            if ($matchedFaq && strlen($matchedFaq->answer) > 10) {
                return $matchedFaq->answer;
            }
        } catch (\Exception $e) {
            // Silence exception if DB unavailable
        }

        // 2. Intent matching par mots-clés
        if (preg_match('/(prix|tarif|combien|coût|cout|budget|factur|devis)/i', $normalized)) {
            return "Nos tarifs sont établis sur-mesure en fonction de vos besoins et des fonctionnalités de votre projet.\n\n👉 Vous pouvez [demander un devis gratuit](/devis) pour obtenir une estimation précise en 24h !";
        }

        if (preg_match('/(service|offre|propos|que faites|developpement|développement|mobile|site|web|ia|intelligence|automatisa|formation)/i', $normalized)) {
            return "Chez **OMF <code/>**, nous accompagnons les entreprises avec :\n- 💻 **Développement Web & Mobile** (sites vitrines, e-commerce, apps iOS/Android)\n- 🤖 **IA & Automatisation** (chatbots, agents IA, automatisation des tâches)\n- 🎨 **Design & UI/UX** (identités visuelles, maquettes)\n- 🎓 **Formations & Conseil** (prompt engineering, transformation digitale)\n\n👉 En savoir plus sur [nos services](/services) ou [demander un devis](/devis).";
        }

        if (preg_match('/(contact|joindre|téléphone|telephone|email|mail|adresse|ouagadougou|burkina|siège|ou)/i', $normalized)) {
            return "Vous pouvez nous contacter directement :\n- 📍 **Localisation** : Ouagadougou, Burkina Faso\n- ✉️ **Email** : via notre formulaire en ligne\n- 📅 **Rendez-vous** : [Prendre rendez-vous](/rendez-vous)\n\n👉 [Envoyez-nous un message](/contact).";
        }

        if (preg_match('/(rendez-vous|rdv|rencontrer|discuter|parler)/i', $normalized)) {
            return "Nous serions ravis de discuter de votre projet avec vous !\n\n👉 Vous pouvez choisir un créneau directement sur notre page de [prise de rendez-vous](/rendez-vous).";
        }

        if (preg_match('/(projet|réalisation|realisation|portfolio|exemple|client|etude de cas)/i', $normalized)) {
            return "Découvrez nos projets récents et études de cas clients sur notre page dédiée.\n\n👉 [Voir nos réalisations](/realisations).";
        }

        if (preg_match('/(blog|article|actualité|actu|news)/i', $normalized)) {
            return "Consultez nos derniers articles et conseils sur la tech, le web et l'intelligence artificielle.\n\n👉 [Lire notre blog](/blog).";
        }

        if (preg_match('/(qui|omf|ousmane|faissal|equipe|équipe|agence|histoire)/i', $normalized)) {
            return "OMF <code/> est une agence de solutions numériques basée à Ouagadougou (Burkina Faso), fondée par Ousmane Martial & Faissal.\n\n👉 [Découvrir l'agence](/about).";
        }

        if (preg_match('/(bonjour|salut|hello|coucou|bonsoir)/i', $normalized)) {
            return "Bonjour 👋 Comment puis-je vous aider aujourd'hui ? Je peux vous renseigner sur nos services de développement, nos solutions IA, nos formations ou nos tarifs.";
        }

        return "Je suis l'assistant IA d'OMF <code/>. Je peux vous renseigner sur nos services de développement web & mobile, nos solutions IA, nos formations et vous accompagner dans vos projets.\n\n👉 Pour toute question spécifique, vous pouvez [nous contacter](/contact) ou [demander un devis](/devis).";
    }
}

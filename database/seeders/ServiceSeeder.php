<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Développement',
                'icon' => '&lt;/&gt;',
                'description' => 'Nous concevons du code robuste, testé et documenté. Vous restez propriétaire.',
                'services' => [
                    ['title' => 'Sites web', 'short_description' => 'Vitrine, e-commerce, SaaS.'],
                    ['title' => 'Applications web', 'short_description' => 'SPA, dashboards, back-office.'],
                    ['title' => 'Applications mobiles', 'short_description' => 'iOS & Android natifs ou Flutter.'],
                    ['title' => 'Logiciels sur mesure', 'short_description' => 'ERP, CRM, outils internes.'],
                ],
            ],
            [
                'name' => 'Intelligence artificielle',
                'icon' => '✦',
                'description' => 'Agents, chatbots, automatisation — de la stratégie au déploiement.',
                'services' => [
                    ['title' => 'Intégration d\'IA', 'short_description' => 'Brancher l\'IA à vos outils existants.'],
                    ['title' => 'Chatbots', 'short_description' => 'Support client 24/7, WhatsApp inclus.'],
                    ['title' => 'Automatisation', 'short_description' => 'Workflows n8n, Make, code-native.'],
                    ['title' => 'IA pour PME', 'short_description' => 'Packs prêts-à-l\'emploi.'],
                    ['title' => 'Prompt Engineering', 'short_description' => 'Prompts qui produisent des résultats.'],
                    ['title' => 'Agents IA', 'short_description' => 'Agents autonomes multi-outils.'],
                ],
            ],
            [
                'name' => 'Formation',
                'icon' => '◎',
                'description' => 'Individuelle, groupe ou entreprise. En présentiel ou distanciel.',
                'services' => [
                    ['title' => 'IA pour débutants', 'short_description' => 'Comprendre et utiliser l\'IA.'],
                    ['title' => 'Formation Prompt Engineering', 'short_description' => 'Bien utiliser ChatGPT et autres.'],
                    ['title' => 'Développement web', 'short_description' => 'HTML → Laravel → déploiement.'],
                    ['title' => 'Word', 'short_description' => 'Rédaction professionnelle.'],
                    ['title' => 'Excel', 'short_description' => 'Fonctions, TCD, dashboards.'],
                    ['title' => 'PowerPoint', 'short_description' => 'Présentations qui convainquent.'],
                ],
            ],
            [
                'name' => 'Conseil',
                'icon' => '◆',
                'description' => 'Vision claire, décisions solides, accompagnement au long cours.',
                'services' => [
                    ['title' => 'Audit informatique', 'short_description' => 'Diagnostic complet sous 2 semaines.'],
                    ['title' => 'Transformation numérique', 'short_description' => 'Feuille de route sur 12 mois.'],
                    ['title' => 'Accompagnement', 'short_description' => 'CTO à temps partagé.'],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $services = $catData['services'];
            unset($catData['services']);
            $slug = Str::slug($catData['name']);
            $catData['slug'] = $slug;

            $category = ServiceCategory::firstOrCreate(
                ['slug' => $slug],
                $catData,
            );

            foreach ($services as $srvData) {
                $slug = Str::slug($srvData['title']);
                $srvData['slug'] = $slug;
                $srvData['content'] = '<p>Détails à venir pour le service <strong>'.$srvData['title'].'</strong>. Vous pouvez modifier ce contenu via le panneau d\'administration.</p>';

                $category->services()->firstOrCreate(
                    ['slug' => $slug],
                    $srvData,
                );
            }
        }
    }
}

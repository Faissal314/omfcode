<?php

namespace Database\Seeders;

use App\Models\FaqQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $questions = [
            [
                'question' => 'Combien coûte un site web ?',
                'answer' => 'Le prix dépend de la complexité, du nombre de pages, du design et des intégrations. Nous proposons un devis sur mesure après un premier échange.',
            ],
            [
                'question' => 'Combien de temps pour livrer un projet ?',
                'answer' => 'Un site vitrine se livre généralement en 2 à 4 semaines, et une application web en 2 à 4 mois. Un chatbot IA peut être livré en 7 à 14 jours selon le périmètre.',
            ],
            [
                'question' => 'Suis-je propriétaire du code ?',
                'answer' => 'Oui. Vous recevez le code source et la documentation. Nous pouvons aussi vous transmettre l’accès au dépôt Git.',
            ],
            [
                'question' => 'Proposez-vous du support après livraison ?',
                'answer' => 'Oui, nous proposons un support initial inclus et des forfaits de maintenance mensuels selon vos besoins.',
            ],
            [
                'question' => 'Pouvez-vous former mon équipe ?',
                'answer' => 'Oui. Nous proposons des formations à distance et en présentiel sur le développement web, IA, automatisation et gestion de projet.',
            ],
        ];

        foreach ($questions as $qa) {
            FaqQuestion::create(array_merge($qa, [
                'source' => 'seed',
                'is_public' => true,
            ]));
        }
    }
}

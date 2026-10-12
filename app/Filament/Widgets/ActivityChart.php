<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\ContactMessage;
use Filament\Widgets\ChartWidget;

class ActivityChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Activité des 30 derniers jours';

    protected ?string $description = 'Évolution des messages et rendez-vous reçus';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn ($i) => now()->subDays($i));

        $labels = $days->map(fn ($d) => $d->format('d/m'))->toArray();

        $messagesData = $days->map(function ($day) {
            return ContactMessage::whereDate('created_at', $day->toDateString())->count();
        })->toArray();

        $appointmentsData = $days->map(function ($day) {
            return Appointment::whereDate('created_at', $day->toDateString())->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Messages de contact',
                    'data' => $messagesData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Rendez-vous',
                    'data' => $appointmentsData,
                    'borderColor' => '#0238e8',
                    'backgroundColor' => 'rgba(2, 56, 232, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

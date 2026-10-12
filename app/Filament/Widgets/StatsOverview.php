<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\Project;
use App\Models\Testimonial;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $unreadMessages = ContactMessage::where('is_read', false)->count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $totalProjects = Project::count();
        $publishedPosts = Post::count();
        $pendingTestimonials = Testimonial::where('is_approved', false)->count();
        $pendingComments = PostComment::where('is_approved', false)->count();

        return [
            Stat::make('Messages non lus', $unreadMessages)
                ->description('Messages de contact en attente')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($unreadMessages > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-envelope'),

            Stat::make('Rendez-vous en attente', $pendingAppointments)
                ->description('Nouveaux rendez-vous à valider')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingAppointments > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-calendar'),

            Stat::make('Réalisations', $totalProjects)
                ->description('Projets en portefeuille')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary')
                ->icon('heroicon-o-briefcase'),

            Stat::make('Articles publiés', $publishedPosts)
                ->description('Articles de blog en ligne')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success')
                ->icon('heroicon-o-document-text'),

            Stat::make('Avis à modérer', $pendingTestimonials)
                ->description('Témoignages en attente')
                ->descriptionIcon('heroicon-m-star')
                ->color($pendingTestimonials > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-star'),

            Stat::make('Commentaires à modérer', $pendingComments)
                ->description('Commentaires blog en attente')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($pendingComments > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-chat-bubble-left-right'),
        ];
    }
}

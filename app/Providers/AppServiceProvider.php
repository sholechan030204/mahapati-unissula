<?php

namespace App\Providers;

use App\Models\AccountRequest;
use App\Models\ActivityChangeRequest;
use App\Models\GalleryChangeRequest;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('public.*', function (): void {
            $settings = SiteSetting::values();
            $whatsappNumber = preg_replace('/\D+/', '', (string) ($settings['contact_whatsapp'] ?? config('public_contact.whatsapp') ?? ''));
            if (str_starts_with($whatsappNumber, '0')) {
                $whatsappNumber = '62'.substr($whatsappNumber, 1);
            }
            $socialDefaults = config('public_contact.social_links');
            $socialSettings = ['Instagram' => 'instagram_url', 'Facebook' => 'facebook_url', 'YouTube' => 'youtube_url', 'TikTok' => 'tiktok_url'];
            foreach ($socialDefaults as &$social) {
                $settingKey = $socialSettings[$social['name']] ?? null;
                if ($settingKey !== null && filled($settings[$settingKey] ?? null)) {
                    $social['url'] = $settings[$settingKey];
                }
            }
            unset($social);
            config([
                'public_contact.email' => $settings['contact_email'] ?? config('public_contact.email'),
                'public_contact.whatsapp' => $settings['contact_whatsapp'] ?? config('public_contact.whatsapp'),
                'public_contact.whatsapp_link_number' => $whatsappNumber,
                'public_contact.social_links' => $socialDefaults,
            ]);
        });

        View::composer('layouts.admin', function (ViewInstance $view): void {
            $user = Auth::user();
            $view->with('unreadDashboardNotifications', $user?->unreadNotifications()->count() ?? 0);
            if (Auth::user()?->isAdmin()) {
                $view->with(
                    'pendingAccountRequests',
                    AccountRequest::query()->where('status', AccountRequest::STATUS_PENDING)->count(),
                );
                $view->with(
                    'pendingActivityRequests',
                    ActivityChangeRequest::query()->where('status', ActivityChangeRequest::STATUS_PENDING)->count(),
                );
                $view->with(
                    'pendingGalleryRequests',
                    GalleryChangeRequest::query()->where('status', GalleryChangeRequest::STATUS_PENDING)->count(),
                );
            }
        });
    }
}

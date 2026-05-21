<?php

namespace App\Services;

use App\Models\CreatorProfile;
use App\Models\Review;
use App\Models\Service;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PlatformStatsService
{
    public static function get(): array
    {
        return Cache::remember('platform_stats', 3600, function () {
            $totalUsers       = User::count();
            $totalFreelancers = User::where('role', 'freelancer')->count();
            $totalServicos    = Service::count();
            $totalCriadores   = CreatorProfile::count();
            $totalPosts30d    = SocialPost::where('created_at', '>=', now()->subDays(30))->count();

            $avgRating        = Review::avg('rating') ?? 0;
            $satisfacao       = $avgRating > 0 ? round(($avgRating / 5) * 100) : 0;

            return compact(
                'totalUsers',
                'totalFreelancers',
                'totalServicos',
                'totalCriadores',
                'totalPosts30d',
                'satisfacao',
            );
        });
    }

    public static function flush(): void
    {
        Cache::forget('platform_stats');
    }

    public static function format(int $n): string
    {
        if ($n >= 1_000_000) return '+' . number_format($n / 1_000_000, 1, ',', '.') . ' M';
        if ($n >= 1_000)     return '+' . number_format($n / 1_000, 1, ',', '.') . ' mil';
        return '+' . number_format($n, 0, ',', '.');
    }
}

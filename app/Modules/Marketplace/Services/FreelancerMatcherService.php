<?php

namespace App\Modules\Marketplace\Services;

use App\Models\User;
use App\Models\Service;
use App\Models\FreelancerProfile;

class FreelancerMatcherService
{
    /**
     * Returns ranked freelancers for a given service.
     */
    public static function match(Service $service, int $limit = 6): \Illuminate\Support\Collection
    {
        $keywords = self::extractKeywords($service);

        $freelancers = User::where('role', 'freelancer')
            ->where('status', 'active')
            ->with(['freelancerProfile', 'reviewsReceived'])
            ->whereHas('freelancerProfile')
            ->get();

        return $freelancers
            ->map(fn(User $u) => [
                'user'  => $u,
                'score' => self::score($u, $keywords),
            ])
            ->filter(fn($item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->values()
            ->map(fn($item) => $item['user']);
    }

    private static function score(User $user, array $keywords): float
    {
        $profile = $user->freelancerProfile;
        $score   = 0.0;

        // Skill keyword overlap (weight 3 each)
        $skills = array_map('mb_strtolower', $profile->skills ?? []);
        foreach ($keywords as $kw) {
            foreach ($skills as $skill) {
                if (str_contains($skill, $kw) || str_contains($kw, $skill)) {
                    $score += 3;
                    break;
                }
            }
        }

        // Average rating (weight 2, max 10)
        $avgRating = $user->averageRating();
        $score += ($avgRating / 5) * 10;

        // Availability bonus
        if ($profile->availability_status === 'disponivel') {
            $score += 4;
        } elseif ($profile->availability_status === 'parcial') {
            $score += 2;
        }

        // Completion rate bonus (completed services / total accepted)
        $completed = \App\Models\Service::where('freelancer_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $total = \App\Models\Service::where('freelancer_id', $user->id)
            ->whereIn('status', ['accepted', 'in_progress', 'delivered', 'completed'])
            ->count();
        if ($total > 0) {
            $score += ($completed / $total) * 5;
        }

        // Bonus for having a complete profile
        if ($profile->headline && $profile->summary) $score += 2;
        if (!empty($profile->skills))               $score += 1;

        return $score;
    }

    private static function extractKeywords(Service $service): array
    {
        $keywords = [];

        // From service_type
        if ($service->service_type) {
            // Map to template keywords
            $tpl = BriefingTemplateService::get($service->service_type);
            if ($tpl) {
                $keywords = array_merge($keywords, $tpl['keywords'] ?? []);
            }
            // Also split the service type string itself
            $parts = preg_split('/[\s,()\/]+/', mb_strtolower($service->service_type));
            $keywords = array_merge($keywords, array_filter($parts, fn($p) => mb_strlen($p) > 3));
        }

        // From briefing text
        if ($service->briefing) {
            $stopwords = ['para', 'como', 'com', 'que', 'por', 'uma', 'um', 'dos', 'das', 'nos', 'nas',
                          'the', 'and', 'for', 'are', 'but', 'not', 'you', 'all', 'can', 'her', 'was'];
            $words = preg_split('/\s+/', mb_strtolower(strip_tags($service->briefing)));
            foreach ($words as $word) {
                $clean = preg_replace('/[^a-zÃ¡Ã©Ã­Ã³ÃºÃ£ÃµÃ¢ÃªÃ´Ã Ã¼]/u', '', $word);
                if (mb_strlen($clean) > 4 && !in_array($clean, $stopwords)) {
                    $keywords[] = $clean;
                }
            }
        }

        return array_unique(array_values($keywords));
    }
}


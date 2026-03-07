<?php

namespace App\Services;

use App\Models\FreelancerProfile;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FreelancerMatchingService
{
    /**
     * Score weights
     */
    private const W_SKILL_MATCH    = 4;  // per skill that matches briefing keywords
    private const W_AVAILABLE      = 3;  // availability_status == 'available'
    private const W_RATING         = 1;  // per star (0-5 scale)
    private const W_PREV_SUCCESS   = 6;  // previously completed a project with this client
    private const W_HAS_PORTFOLIO  = 1;  // has at least one portfolio item
    private const W_BUSY           = -1; // small penalty for busy

    /**
     * Return top $limit freelancers ranked by relevance to the given service.
     */
    public function recommend(Service $service, int $limit = 6): Collection
    {
        $keywords = $this->extractKeywords($service->titulo . ' ' . $service->briefing);

        // Load all active freelancers with their profiles & portfolios (eager load)
        $freelancers = User::where('role', 'freelancer')
            ->where('id', '!=', $service->cliente_id)
            ->with(['freelancerProfile', 'portfolios'])
            ->get();

        // Pre-fetch IDs of freelancers already hired/completed with this client
        $prevSuccessIds = Service::where('cliente_id', $service->cliente_id)
            ->where('status', 'completed')
            ->whereNotNull('freelancer_id')
            ->pluck('freelancer_id')
            ->flip(); // flip to hash for O(1) lookup

        // Pre-fetch IDs of freelancers already candidates for this service
        $alreadyCandidateIds = $service->candidates()->pluck('freelancer_id')->flip();

        $scored = $freelancers->map(function (User $freelancer) use ($keywords, $prevSuccessIds, $alreadyCandidateIds) {
            $fp     = $freelancer->freelancerProfile;
            $score  = 0;
            $matches = [];

            if (!$fp) {
                return null;
            }

            // Skill matching
            $skills = array_map('mb_strtolower', (array) ($fp->skills ?? []));
            foreach ($keywords as $kw) {
                foreach ($skills as $skill) {
                    if (str_contains($skill, $kw) || str_contains($kw, $skill)) {
                        $score   += self::W_SKILL_MATCH;
                        $matches[] = $skill;
                        break; // count each skill once per keyword
                    }
                }
            }

            // Availability
            if ($fp->availability_status === 'available') {
                $score += self::W_AVAILABLE;
            } elseif ($fp->availability_status === 'busy') {
                $score += self::W_BUSY;
            }

            // Rating
            $metrics = is_array($fp->metrics) ? $fp->metrics : (json_decode($fp->metrics, true) ?? []);
            $rating  = (float) ($metrics['rating'] ?? 0);
            $score  += $rating * self::W_RATING;

            // Previous successful collaboration
            if (isset($prevSuccessIds[$freelancer->id])) {
                $score += self::W_PREV_SUCCESS;
            }

            // Has portfolio
            if ($freelancer->portfolios->isNotEmpty()) {
                $score += self::W_HAS_PORTFOLIO;
            }

            return [
                'freelancer'     => $freelancer,
                'score'          => round($score, 2),
                'skill_matches'  => array_unique($matches),
                'rating'         => $rating,
                'metrics'        => $metrics,
                'is_candidate'   => isset($alreadyCandidateIds[$freelancer->id]),
            ];
        })
        ->filter()
        ->sortByDesc('score')
        ->take($limit)
        ->values();

        return $scored;
    }

    /**
     * Extract significant lowercase keywords from a block of text.
     * Removes stopwords and very short tokens.
     */
    private function extractKeywords(string $text): array
    {
        $stopwords = [
            'de','do','da','dos','das','um','uma','uns','umas','o','a','os','as',
            'para','com','por','em','no','na','nos','nas','ao','aos',
            'que','como','mais','muito','quando','onde','se','mas','ou',
            'e','é','são','ser','ter','tem','para','sobre','criar','fazer',
            'need','the','with','and','for','of','to','a','an','in','on',
        ];

        $text   = Str::lower($text);
        $text   = preg_replace('/[^a-záéíóúâêîôûãõàèìòùäëïöüç\s]/u', ' ', $text);
        $tokens = preg_split('/\s+/', trim($text));

        return array_values(array_unique(
            array_filter($tokens, fn($t) => strlen($t) >= 3 && !in_array($t, $stopwords))
        ));
    }
}

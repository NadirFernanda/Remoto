<?php

namespace App\Modules\Marketplace\Controllers;

use Illuminate\Routing\Controller;
use App\Models\User;

class FreelancerProfileController extends Controller
{
    public function show(User $user)
    {
        $viewer = auth()->user();
        $isOwnerOrAdmin = $viewer && ($viewer->id === $user->id || $viewer->role === 'admin');

        if ($user->kyc_status !== 'verified' && !$isOwnerOrAdmin) {
            return view('profile-unavailable');
        }

        $user->load([
            'freelancerProfile',
            'portfolios' => fn ($q) => $q->where('is_public', true)->orderBy('sort_order'),
            'servicesAsFreelancer',
            'reviewsReceived.author',
            'workExperiences',
            'educations',
        ]);
        $avgRating  = $user->averageRating();
        $reviewCount = $user->reviewsReceived()->count();

        return view('freelancer.show', compact('user', 'avgRating', 'reviewCount'));
    }
}

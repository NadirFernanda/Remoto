<?php

namespace App\Modules\Marketplace\Controllers;

use Illuminate\Routing\Controller;
use App\Models\User;

class ClientProfileController extends Controller
{
    public function show(User $user)
    {
        $viewer = auth()->user();
        $isOwnerOrAdmin = $viewer && ($viewer->id === $user->id || $viewer->role === 'admin');

        if ($user->kyc_status !== 'verified' && !$isOwnerOrAdmin) {
            return view('profile-unavailable');
        }

        $user->load('profile');

        $completedProjects = $user->servicesAsClient()->where('status', 'completed')->count();

        return view('client.show', compact('user', 'completedProjects'));
    }
}

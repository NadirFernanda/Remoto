<?php

namespace App\Modules\Marketplace\Controllers;

use Illuminate\Routing\Controller;
use App\Models\User;

class ClientProfileController extends Controller
{
    public function show(User $user)
    {
        $user->load('profile');

        $completedProjects = $user->servicesAsClient()->where('status', 'completed')->count();

        return view('client.show', compact('user', 'completedProjects'));
    }
}

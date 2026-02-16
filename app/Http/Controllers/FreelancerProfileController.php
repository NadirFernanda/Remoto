<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class FreelancerProfileController extends Controller
{
    public function show(User $user)
    {
        $user->load(['freelancerProfile', 'portfolios', 'servicesAsFreelancer']);
        return view('freelancer.show', compact('user'));
    }
}

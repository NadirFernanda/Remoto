<?php

namespace App\Services;

use App\Models\User;

class FreelancerService
{
    public static function getAllFreelancers()
    {
        return User::where('role', 'freelancer')->get();
    }
}


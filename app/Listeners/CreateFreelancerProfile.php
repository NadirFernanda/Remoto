<?php

namespace App\Listeners;

use App\Events\FreelancerRegistered;
use App\Models\Profile;
use App\Models\CreatorProfile;
use App\Models\FreelancerProfile;

class CreateFreelancerProfile
{
    public function handle(FreelancerRegistered $event)
    {
        $user = $event->user;

        Profile::firstOrCreate(['user_id' => $user->id]);

        FreelancerProfile::firstOrCreate(['user_id' => $user->id]);

        // Freelancers têm acesso ao módulo criador - perfil criado automaticamente
        CreatorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['is_public' => true]
        );
    }
}

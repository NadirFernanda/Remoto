<?php

namespace App\Listeners;

use App\Events\FreelancerRegistered;
use App\Models\Profile;

class CreateFreelancerProfile
{
    public function handle(FreelancerRegistered $event)
    {
        Profile::create([
            'user_id' => $event->user->id,
            // Adicione outros campos padrão do perfil aqui
        ]);
    }
}

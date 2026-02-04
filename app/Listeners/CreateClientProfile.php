<?php

namespace App\Listeners;

use App\Events\ClientRegistered;
use App\Models\Profile;

class CreateClientProfile
{
    public function handle(ClientRegistered $event)
    {
        Profile::create([
            'user_id' => $event->user->id,
            // Adicione outros campos padrão do perfil aqui
        ]);
    }
}

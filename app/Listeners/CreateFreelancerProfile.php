<?php

namespace App\Listeners;

use App\Events\FreelancerRegistered;
use App\Models\CreatorProfile;
use App\Models\FreelancerProfile;
use App\Models\Profile;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class CreateFreelancerProfile
{
    public function handle(FreelancerRegistered $event)
    {
        $user = $event->user;

        DB::transaction(function () use ($user) {
            Profile::firstOrCreate(['user_id' => $user->id]);

            FreelancerProfile::firstOrCreate(['user_id' => $user->id]);

            // Freelancers têm acesso ao módulo criador - perfil criado automaticamente
            CreatorProfile::firstOrCreate(
                ['user_id' => $user->id],
                ['is_public' => true]
            );

            // Criar carteira para o freelancer — necessária para FinancialPanel e saques.
            // Sem este registo, firstOrFail() nas páginas financeiras causa 500.
            Wallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'saldo'          => 0,
                    'saldo_pendente' => 0,
                    'saque_minimo'   => 1000,
                    'taxa_saque'     => 0,
                ]
            );
        });
    }
}

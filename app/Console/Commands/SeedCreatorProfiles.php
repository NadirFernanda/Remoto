<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CreatorProfile;

class SeedCreatorProfiles extends Command
{
    protected $signature   = 'seed:creator-profiles';
    protected $description = 'Cria CreatorProfile para todos os freelancers que ainda não têm um';

    public function handle(): int
    {
        $users = User::where(function ($q) {
            $q->where('role', 'creator')
              ->orWhere('has_creator_profile', true);
        })->get();

        $created = 0;
        foreach ($users as $user) {
            $wasCreated = CreatorProfile::firstOrCreate(
                ['user_id' => $user->id],
                ['is_public' => true]
            )->wasRecentlyCreated;

            if ($wasCreated) {
                $created++;
            }
        }

        $this->info("Concluído: {$created} perfis de criador criados (de {$users->count()} utilizadores).");
        return self::SUCCESS;
    }
}

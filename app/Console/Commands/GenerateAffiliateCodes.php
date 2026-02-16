<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GenerateAffiliateCodes extends Command
{
    protected $signature = 'generate:affiliate-codes {--force : Overwrite existing codes}';
    protected $description = 'Generate unique affiliate codes for users missing them';

    public function handle()
    {
        $force = $this->option('force');
        $query = User::query();
        if (!$force) {
            $query->whereNull('affiliate_code');
        }

        $total = 0;
        $query->chunkById(100, function($users) use (&$total) {
            foreach ($users as $user) {
                // generate unique 8-char code
                do {
                    $code = strtoupper(substr(bin2hex(random_bytes(6)), 0, 8));
                } while (User::where('affiliate_code', $code)->exists());

                $user->affiliate_code = $code;
                $user->save();
                $total++;
                $this->info("Generated code {$code} for user {$user->id}");
            }
        });

        $this->info("Done. Generated affiliate codes for {$total} users.");
        return 0;
    }
}

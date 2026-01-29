<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;
use App\Services\BriefingTextGenerator;

class ConvertBriefingsToText extends Command
{
    protected $signature = 'briefings:convert-to-text';
    protected $description = 'Converte todos os briefings antigos (array/json) para texto profissional.';

    public function handle()
    {
        $count = 0;
        $services = Service::all();
        foreach ($services as $service) {
            $briefing = $service->briefing;
            $briefingArray = @json_decode($briefing, true);
            if (is_array($briefingArray) && isset($briefingArray['business_type'])) {
                $text = BriefingTextGenerator::generate($briefingArray);
                $service->briefing = $text;
                $service->save();
                $count++;
            }
        }
        $this->info("{$count} briefings convertidos para texto profissional.");
        return 0;
    }
}

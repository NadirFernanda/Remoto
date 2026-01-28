<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;

class DeleteAllServices extends Command
{
    protected $signature = 'services:delete-all';
    protected $description = 'Apaga todos os registros da tabela services (pedidos)';

    public function handle()
    {
        $count = Service::count();
        Service::truncate();
        $this->info("Apagados $count pedidos da tabela services.");
    }
}
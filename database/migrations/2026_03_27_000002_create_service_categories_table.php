<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();        // e.g. emoji or heroicon name
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default categories
        $now = now();
        DB::table('service_categories')->insert([
            ['name' => 'Design Gráfico',         'slug' => 'design-grafico',          'icon' => '🎨', 'description' => 'Logótipos, branding, ilustrações e identidade visual.',     'active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Desenvolvimento Web',     'slug' => 'desenvolvimento-web',      'icon' => '💻', 'description' => 'Sites, landing pages, aplicações web.',                     'active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Marketing Digital',       'slug' => 'marketing-digital',        'icon' => '📢', 'description' => 'SEO, redes sociais, gestão de anúncios.',                   'active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Redação & Conteúdo',      'slug' => 'redacao-conteudo',         'icon' => '✍️', 'description' => 'Artigos, copywriting, roteiros e tradução.',               'active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vídeo & Animação',        'slug' => 'video-animacao',           'icon' => '🎬', 'description' => 'Edição de vídeo, motion graphics e animações.',            'active' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Música & Áudio',          'slug' => 'musica-audio',             'icon' => '🎵', 'description' => 'Produção musical, locução e podcasts.',                    'active' => true, 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Programação & Tech',      'slug' => 'programacao-tech',         'icon' => '🛠️', 'description' => 'APIs, apps móveis, automações e scripts.',                 'active' => true, 'sort_order' => 7, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Consultoria & Negócios',  'slug' => 'consultoria-negocios',     'icon' => '📊', 'description' => 'Planos de negócio, finanças, estratégia.',                 'active' => true, 'sort_order' => 8, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fotografia',              'slug' => 'fotografia',               'icon' => '📷', 'description' => 'Fotografia de produto, eventos e retratos.',               'active' => true, 'sort_order' => 9, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Outros',                  'slug' => 'outros',                   'icon' => '📌', 'description' => 'Serviços que não se enquadram nas categorias acima.',      'active' => true, 'sort_order' => 99, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};

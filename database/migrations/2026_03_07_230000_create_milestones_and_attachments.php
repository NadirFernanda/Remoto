<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Milestones per service
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // File attachments per service
        Schema::create('service_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('filename', 255);
            $table->string('path', 500);
            $table->unsignedBigInteger('size')->default(0);
            $table->string('mime_type', 100)->nullable();
            $table->timestamps();
        });

        // Add service_type to services for matching
        Schema::table('services', function (Blueprint $table) {
            $table->string('service_type', 150)->nullable()->after('briefing');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_attachments');
        Schema::dropIfExists('milestones');
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('service_type');
        });
    }
};

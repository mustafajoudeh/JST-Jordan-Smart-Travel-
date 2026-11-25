<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->cascadeOnDelete();
            $table->foreignId('experience_id')->nullable()->constrained('experiences')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'destination_id']);
            $table->unique(['user_id', 'service_id']);
            $table->unique(['user_id', 'experience_id']);
        });

        DB::statement('ALTER TABLE favorites ADD CONSTRAINT favorites_exactly_one_target_check CHECK ((destination_id IS NOT NULL) + (service_id IS NOT NULL) + (experience_id IS NOT NULL) = 1)');
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
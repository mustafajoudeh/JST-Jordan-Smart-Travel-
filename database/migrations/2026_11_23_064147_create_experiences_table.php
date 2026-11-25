<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained('destinations')->restrictOnDelete();
            $table->string('title', 150);
            $table->text('body');
            $table->smallInteger('rating');
            $table->string('social_url', 500)->nullable();
            $table->json('tags')->nullable();
            $table->string('status', 10)->default('pending');
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index(['destination_id', 'status']);
        });

        DB::statement('ALTER TABLE experiences ADD CONSTRAINT experiences_rating_check CHECK (rating BETWEEN 1 AND 5)');
        DB::statement("ALTER TABLE experiences ADD CONSTRAINT experiences_social_url_check CHECK (social_url IS NULL OR social_url LIKE 'https://%')");
        DB::statement("ALTER TABLE experiences ADD CONSTRAINT experiences_status_check CHECK (status IN ('pending','approved','rejected','hidden'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
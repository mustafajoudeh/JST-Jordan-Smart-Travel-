<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('governorate_id')->constrained('governorates')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name', 150);
            $table->text('description');
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            $table->string('address', 255)->nullable();
            $table->smallInteger('suggested_duration_min')->nullable();
            $table->decimal('estimated_cost', 8, 2)->nullable();
            $table->json('opening_hours')->nullable();
            $table->json('tags')->nullable(); // replaces PostgreSQL text[]
            $table->string('source_type', 10)->default('curated');
            $table->string('source_url', 500)->nullable();
            $table->date('last_verified_at')->nullable();
            $table->string('status', 10)->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->unique(['governorate_id', 'name']);
            $table->index(['governorate_id', 'status']);
            $table->index('category_id');
        });

        DB::statement('ALTER TABLE destinations ADD CONSTRAINT destinations_latitude_check CHECK (latitude BETWEEN -90 AND 90)');
        DB::statement('ALTER TABLE destinations ADD CONSTRAINT destinations_longitude_check CHECK (longitude BETWEEN -180 AND 180)');
        DB::statement('ALTER TABLE destinations ADD CONSTRAINT destinations_suggested_duration_check CHECK (suggested_duration_min > 0)');
        DB::statement('ALTER TABLE destinations ADD CONSTRAINT destinations_estimated_cost_check CHECK (estimated_cost >= 0)');
        DB::statement("ALTER TABLE destinations ADD CONSTRAINT destinations_source_type_check CHECK (source_type IN ('official','curated'))");
        DB::statement("ALTER TABLE destinations ADD CONSTRAINT destinations_status_check CHECK (status IN ('draft','published','inactive'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
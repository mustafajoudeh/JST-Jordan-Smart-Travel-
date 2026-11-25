<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name', 150);
            $table->text('description');
            $table->smallInteger('duration_min')->nullable();
            $table->decimal('estimated_cost', 8, 2)->nullable();
            $table->json('available_times')->nullable();
            $table->string('source_type', 10)->default('curated');
            $table->string('source_url', 500)->nullable();
            $table->date('last_verified_at')->nullable();
            $table->string('status', 10)->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->unique(['destination_id', 'name']);
            $table->index(['destination_id', 'status']);
        });

        DB::statement('ALTER TABLE activities ADD CONSTRAINT activities_duration_min_check CHECK (duration_min > 0)');
        DB::statement('ALTER TABLE activities ADD CONSTRAINT activities_estimated_cost_check CHECK (estimated_cost >= 0)');
        DB::statement("ALTER TABLE activities ADD CONSTRAINT activities_source_type_check CHECK (source_type IN ('official','curated'))");
        DB::statement("ALTER TABLE activities ADD CONSTRAINT activities_status_check CHECK (status IN ('draft','published','inactive'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
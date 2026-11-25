<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 150);
            $table->date('start_date');
            $table->date('end_date');
            $table->smallInteger('travelers_count')->default(1);
            $table->string('budget_level', 10)->nullable();
            $table->string('group_type', 10)->nullable();
            $table->string('starting_point', 150)->nullable();
            $table->boolean('is_ai_generated')->default(false);
            $table->string('status', 10)->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index(['user_id', 'start_date']);
        });

        DB::statement('ALTER TABLE trips ADD CONSTRAINT trips_dates_check CHECK (end_date >= start_date)');
        DB::statement('ALTER TABLE trips ADD CONSTRAINT trips_travelers_count_check CHECK (travelers_count > 0)');
        DB::statement("ALTER TABLE trips ADD CONSTRAINT trips_budget_level_check CHECK (budget_level IN ('low','medium','high'))");
        DB::statement("ALTER TABLE trips ADD CONSTRAINT trips_group_type_check CHECK (group_type IN ('solo','couple','family','friends'))");
        DB::statement("ALTER TABLE trips ADD CONSTRAINT trips_status_check CHECK (status IN ('draft','saved'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
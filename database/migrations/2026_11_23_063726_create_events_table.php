<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('governorate_id')->constrained('governorates')->restrictOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->string('name', 150);
            $table->text('description');
            $table->string('event_type', 25);
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('venue_name', 150)->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->string('source_type', 10)->default('curated');
            $table->string('source_url', 500)->nullable();
            $table->date('last_verified_at')->nullable();
            $table->string('status', 10)->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index(['governorate_id', 'start_date', 'end_date']);
        });

        DB::statement("ALTER TABLE events ADD CONSTRAINT events_event_type_check CHECK (event_type IN ('cultural','festival','exhibition','food_art','sports_entertainment','national'))");
        DB::statement('ALTER TABLE events ADD CONSTRAINT events_dates_check CHECK (end_date >= start_date)');
        DB::statement("ALTER TABLE events ADD CONSTRAINT events_source_type_check CHECK (source_type IN ('official','curated'))");
        DB::statement("ALTER TABLE events ADD CONSTRAINT events_status_check CHECK (status IN ('draft','published','inactive'))");
        DB::statement("ALTER TABLE events ADD CONSTRAINT events_published_requirements_check CHECK (status <> 'published' OR (source_url IS NOT NULL AND (destination_id IS NOT NULL OR venue_name IS NOT NULL)))");
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
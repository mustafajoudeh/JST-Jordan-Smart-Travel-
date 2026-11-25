<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->smallInteger('day_number');
            $table->smallInteger('position');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->restrictOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained('activities')->restrictOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->restrictOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->restrictOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->string('notes', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->unique(['trip_id', 'day_number', 'position']);
            $table->unique('booking_id');
        });

        DB::statement('ALTER TABLE trip_items ADD CONSTRAINT trip_items_day_number_check CHECK (day_number >= 1)');
        DB::statement('ALTER TABLE trip_items ADD CONSTRAINT trip_items_exactly_one_item_check CHECK ((destination_id IS NOT NULL) + (activity_id IS NOT NULL) + (event_id IS NOT NULL) + (service_id IS NOT NULL) = 1)');
        DB::statement('ALTER TABLE trip_items ADD CONSTRAINT trip_items_booking_requires_service_check CHECK (booking_id IS NULL OR service_id IS NOT NULL)');
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_items');
    }
};
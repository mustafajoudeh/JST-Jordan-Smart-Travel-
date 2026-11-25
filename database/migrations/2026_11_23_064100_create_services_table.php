<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name', 150);
            $table->text('description');
            $table->smallInteger('duration_min')->nullable();
            $table->decimal('price', 8, 3)->nullable();
            $table->string('price_unit', 10)->default('per_person');
            $table->boolean('booking_enabled')->default(false);
            $table->smallInteger('max_participants')->nullable();
            $table->smallInteger('daily_capacity')->nullable();
            $table->decimal('commission_rate', 5, 4)->nullable();
            $table->char('currency', 3)->default('JOD');
            $table->string('status', 10)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index(['destination_id', 'status']);
        });

        DB::statement('ALTER TABLE services ADD CONSTRAINT services_duration_min_check CHECK (duration_min > 0)');
        DB::statement('ALTER TABLE services ADD CONSTRAINT services_price_check CHECK (price >= 0)');
        DB::statement("ALTER TABLE services ADD CONSTRAINT services_price_unit_check CHECK (price_unit IN ('per_person','per_group'))");
        DB::statement("ALTER TABLE services ADD CONSTRAINT services_status_check CHECK (status IN ('pending','approved','rejected','inactive'))");
        DB::statement('ALTER TABLE services ADD CONSTRAINT services_booking_price_check CHECK (booking_enabled = 0 OR price IS NOT NULL)');
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
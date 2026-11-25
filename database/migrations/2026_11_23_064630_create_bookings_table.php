<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->date('booking_date');
            $table->time('start_time')->nullable();
            $table->smallInteger('participants');
            $table->decimal('unit_price', 8, 3);
            $table->decimal('total_price', 10, 3);
            $table->char('currency', 3)->default('JOD');
            $table->decimal('commission_rate', 5, 4);
            $table->decimal('commission_amount', 10, 3);
            $table->decimal('provider_net', 10, 3);
            $table->string('status', 10)->default('pending');
            $table->string('payment_status', 20)->default('unpaid');
            $table->string('contact_phone', 30)->nullable();
            $table->string('customer_note', 500)->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['service_id', 'booking_date', 'status']);
        });

        DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_participants_check CHECK (participants > 0)');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_unit_price_check CHECK (unit_price >= 0)');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_total_price_check CHECK (total_price >= 0)');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_commission_rate_check CHECK (commission_rate BETWEEN 0 AND 1)');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_commission_amount_check CHECK (commission_amount >= 0)');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_provider_net_check CHECK (provider_net >= 0)');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_total_split_check CHECK (total_price = commission_amount + provider_net)');
        DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_status_check CHECK (status IN ('pending','confirmed','rejected','cancelled','completed'))");
        DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_payment_status_check CHECK (payment_status IN ('unpaid','paid','failed','partially_refunded','refunded'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
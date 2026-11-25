<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->restrictOnDelete();
            $table->string('type', 10);
            $table->foreignId('parent_payment_id')->nullable()->constrained('payments')->restrictOnDelete();
            $table->decimal('amount', 10, 3);
            $table->char('currency', 3)->default('JOD');
            $table->string('method', 20);
            $table->string('gateway', 30)->nullable();
            $table->string('gateway_reference', 100)->nullable();
            $table->string('status', 10)->default('pending');
            $table->string('failure_reason', 255)->nullable();
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->unsignedBigInteger('succeeded_charge_booking_id')->nullable()
                ->virtualAs("IF(type = 'charge' AND status = 'succeeded', booking_id, NULL)");
            $table->unique('succeeded_charge_booking_id', 'payments_one_succeeded_charge_per_booking');

            $table->unique(['gateway', 'gateway_reference']);
            $table->index(['booking_id', 'created_at']);
            $table->index('status');
        });

        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_type_check CHECK (type IN ('charge','refund'))");
        DB::statement('ALTER TABLE payments ADD CONSTRAINT payments_amount_check CHECK (amount > 0)');
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_method_check CHECK (method IN ('card','cliq','wallet','cash'))");
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('pending','succeeded','failed'))");
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_type_parent_check CHECK ((type = 'charge' AND parent_payment_id IS NULL) OR (type = 'refund' AND parent_payment_id IS NOT NULL))");
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_succeeded_paid_at_check CHECK (status <> 'succeeded' OR paid_at IS NOT NULL)");
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
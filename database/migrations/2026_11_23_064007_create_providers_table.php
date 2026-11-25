<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('provider_type', 10);
            $table->string('display_name', 150);
            $table->text('description')->nullable();
            $table->string('contact_email', 150)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('website_url', 300)->nullable();
            $table->string('address', 255)->nullable();
            $table->foreignId('governorate_id')->nullable()->constrained('governorates')->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->json('languages')->nullable();
            $table->json('specializations')->nullable();
            $table->string('verification_status', 10)->default('unverified');
            $table->string('status', 10)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->decimal('commission_rate', 5, 4)->nullable();
            $table->boolean('can_receive_bookings')->default(false);
            $table->string('payout_method', 20)->nullable();
            $table->string('payout_account', 255)->nullable(); // encrypted cast may need text
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('status');
        });

        DB::statement("ALTER TABLE providers ADD CONSTRAINT providers_provider_type_check CHECK (provider_type IN ('company','guide'))");
        DB::statement("ALTER TABLE providers ADD CONSTRAINT providers_verification_status_check CHECK (verification_status IN ('unverified','verified'))");
        DB::statement("ALTER TABLE providers ADD CONSTRAINT providers_status_check CHECK (status IN ('pending','approved','rejected','inactive'))");
        DB::statement("ALTER TABLE providers ADD CONSTRAINT providers_payout_method_check CHECK (payout_method IN ('bank_transfer','cliq'))");
        DB::statement("ALTER TABLE providers ADD CONSTRAINT providers_booking_requirements_check CHECK (can_receive_bookings = 0 OR (status = 'approved' AND payout_account IS NOT NULL))");
    }

    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('role', 20)->default('traveler');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 150)->unique();
            $table->string('password_hash', 255);
            $table->char('country_code', 2)->nullable();
            $table->string('photo_url', 500)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('budget_level', 10)->nullable();
            $table->string('trip_pace', 10)->nullable();
            $table->string('group_type', 10)->nullable();
            $table->string('activity_level', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('failed_login_attempts')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('traveler','provider','content_admin','system_admin'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_budget_level_check CHECK (budget_level IN ('low','medium','high'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_trip_pace_check CHECK (trip_pace IN ('relaxed','balanced','packed'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_group_type_check CHECK (group_type IN ('solo','couple','family','friends'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_activity_level_check CHECK (activity_level IN ('low','moderate','high'))");
        
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('token_hash', 255)->unique();
            $table->dateTime('expires_at');
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by_ip', 45)->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoked_by_ip', 45)->nullable();
            $table->foreignId('replaced_by_token_id')
                ->nullable()
                ->constrained('refresh_tokens')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            // "key" is reserved in MariaDB; Laravel quotes it with backticks.
            $table->string('key', 60)->primary();
            $table->string('value', 255);
            $table->string('value_type', 10);
            $table->string('description', 255)->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('updated_at')->useCurrent();
        });

        DB::statement("ALTER TABLE platform_settings ADD CONSTRAINT platform_settings_value_type_check CHECK (value_type IN ('string','decimal','integer','boolean'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
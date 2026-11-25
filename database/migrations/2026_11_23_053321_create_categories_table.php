<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 10);
            $table->string('name', 60);
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->unique(['kind', 'name']);
        });

        DB::statement("ALTER TABLE categories ADD CONSTRAINT categories_kind_check CHECK (kind IN ('tourism','service'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
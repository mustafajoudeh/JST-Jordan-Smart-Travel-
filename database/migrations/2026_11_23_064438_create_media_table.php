<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('file_path', 500);
            $table->string('mime_type', 50);
            $table->integer('file_size_bytes');
            $table->boolean('is_primary')->default(false);
            $table->smallInteger('sort_order')->default(0);
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->cascadeOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained('activities')->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->cascadeOnDelete();
            $table->foreignId('experience_id')->nullable()->constrained('experiences')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unsignedBigInteger('primary_destination_id')->nullable()->virtualAs('IF(is_primary = 1, destination_id, NULL)');
            $table->unsignedBigInteger('primary_activity_id')->nullable()->virtualAs('IF(is_primary = 1, activity_id, NULL)');
            $table->unsignedBigInteger('primary_event_id')->nullable()->virtualAs('IF(is_primary = 1, event_id, NULL)');
            $table->unsignedBigInteger('primary_experience_id')->nullable()->virtualAs('IF(is_primary = 1, experience_id, NULL)');

            $table->unique('primary_destination_id', 'media_one_primary_per_destination');
            $table->unique('primary_activity_id', 'media_one_primary_per_activity');
            $table->unique('primary_event_id', 'media_one_primary_per_event');
            $table->unique('primary_experience_id', 'media_one_primary_per_experience');
        });

        DB::statement("ALTER TABLE media ADD CONSTRAINT media_mime_type_check CHECK (mime_type IN ('image/jpeg','image/png','image/webp'))");
        DB::statement('ALTER TABLE media ADD CONSTRAINT media_file_size_check CHECK (file_size_bytes > 0)');
        DB::statement('ALTER TABLE media ADD CONSTRAINT media_exactly_one_parent_check CHECK ((destination_id IS NOT NULL) + (activity_id IS NOT NULL) + (event_id IS NOT NULL) + (experience_id IS NOT NULL) = 1)');
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
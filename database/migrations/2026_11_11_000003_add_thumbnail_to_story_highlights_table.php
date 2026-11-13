<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('story_highlights', function (Blueprint $table) {
            $table->string('thumbnail_path')->nullable();
            $table->foreignId('thumbnail_story_id')
                ->nullable()
                ->constrained('stories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('story_highlights', function (Blueprint $table) {
            $table->dropConstrainedForeignId('thumbnail_story_id');
            $table->dropColumn('thumbnail_path');
        });
    }
};

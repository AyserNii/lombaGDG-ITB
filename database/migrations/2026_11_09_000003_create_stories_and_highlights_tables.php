<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('image');
            $table->text('caption')->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });

        Schema::create('story_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 40);
            $table->timestamps();
        });

        Schema::create('story_highlight_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_highlight_id')->constrained()->cascadeOnDelete();
            $table->foreignId('story_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['story_highlight_id', 'story_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_highlight_stories');
        Schema::dropIfExists('story_highlights');
        Schema::dropIfExists('stories');
    }
};

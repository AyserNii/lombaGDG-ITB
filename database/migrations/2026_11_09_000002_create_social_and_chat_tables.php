<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('location');
            }
            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->default('prefer_not_to_say')->after('website');
            }
            if (!Schema::hasColumn('users', 'note')) {
                $table->string('note')->nullable()->after('gender');
            }
        });

        if (!Schema::hasTable('follows')) {
            Schema::create('follows', function (Blueprint $table) {
                $table->id();
                $table->foreignId('follower_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('following_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['follower_id', 'following_id']);
            });
        }

        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('image');
                $table->text('caption')->nullable();
                $table->string('type')->default('POST');
                $table->integer('likes_count')->default(0);
                $table->integer('comments_count')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('saved_posts')) {
            Schema::create('saved_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'post_id']);
            });
        }

        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
                $table->text('message');
                $table->text('media_url')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type')->default('general');
                $table->string('message');
                $table->foreignId('post_id')->nullable()->constrained('posts')->nullOnDelete();
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('saved_posts');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('follows');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'bio', 'website', 'gender', 'note']);
        });
    }
};


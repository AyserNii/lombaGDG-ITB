<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('food_sessions')) {
            Schema::create('food_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('host_id')->constrained('users')->cascadeOnDelete();
                $table->string('merchant_name');
                $table->string('category')->default('Makanan Nusantara');
                $table->text('description')->nullable();
                $table->string('pickup_point');
                $table->string('location')->default('Beji, Depok');
                $table->text('image')->nullable();
                $table->unsignedInteger('slots_max')->default(5);
                $table->unsignedInteger('normal_ongkir')->default(15000);
                $table->dateTime('order_deadline')->nullable();
                $table->json('popular_menus')->nullable();
                $table->decimal('rating', 3, 1)->default(4.8);
                $table->string('status')->default('open'); // open, closed, completed
                $table->unsignedInteger('likes_count')->default(0);
                $table->unsignedInteger('comments_count')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('food_session_participants')) {
            Schema::create('food_session_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('food_session_id')->constrained('food_sessions')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('order_items')->nullable();
                $table->unsignedInteger('estimated_cost')->default(0);
                $table->text('notes')->nullable();
                $table->string('status')->default('joined');
                $table->timestamps();

                $table->unique(['food_session_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('food_session_comments')) {
            Schema::create('food_session_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('food_session_id')->constrained('food_sessions')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('comment');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('food_session_likes')) {
            Schema::create('food_session_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('food_session_id')->constrained('food_sessions')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['food_session_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('food_session_likes');
        Schema::dropIfExists('food_session_comments');
        Schema::dropIfExists('food_session_participants');
        Schema::dropIfExists('food_sessions');
    }
};

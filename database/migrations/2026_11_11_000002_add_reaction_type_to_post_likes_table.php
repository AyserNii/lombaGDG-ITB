<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('post_likes', 'reaction')) {
            Schema::table('post_likes', function (Blueprint $table) {
                $table->string('reaction', 10)->default('like');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('post_likes', 'reaction')) {
            Schema::table('post_likes', function (Blueprint $table) {
                $table->dropColumn('reaction');
            });
        }
    }
};

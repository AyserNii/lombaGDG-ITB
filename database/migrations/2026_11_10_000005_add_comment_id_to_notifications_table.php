<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'comment_id')) {
                $table->foreignId('comment_id')
                    ->nullable()
                    ->after('post_id')
                    ->constrained('comments')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'comment_id')) {
                $table->dropConstrainedForeignId('comment_id');
            }
        });
    }
};

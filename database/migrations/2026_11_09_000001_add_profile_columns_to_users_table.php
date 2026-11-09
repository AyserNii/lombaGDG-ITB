<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('Customer · Cluster Beji')->after('password');
            $table->string('role_type')->default('customer')->after('role');
            $table->string('location')->default('Beji, Depok')->after('role_type');
            $table->text('avatar')->nullable()->after('location');
            $table->string('phone')->nullable()->after('avatar');
            $table->string('savings')->default('Rp 95.4k')->after('phone');
            $table->integer('sessions_count')->default(12)->after('savings');
            $table->string('co2_saved')->default('28.4 kg')->after('sessions_count');
            $table->string('badge')->default('Top Saver')->after('co2_saved');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'role_type',
                'location',
                'avatar',
                'phone',
                'savings',
                'sessions_count',
                'co2_saved',
                'badge',
            ]);
        });
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'location_latitude')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('location_latitude', 10, 7)->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'location_longitude')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('location_longitude', 10, 7)->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'location_country')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('location_country', 100)->nullable();
            });
        }
    }

    public function down(): void
    {
        // Keep repaired columns because the original schema state cannot be inferred safely.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'location_latitude')) {
                $table->decimal('location_latitude', 10, 7)->nullable();
            }

            if (! Schema::hasColumn('users', 'location_longitude')) {
                $table->decimal('location_longitude', 10, 7)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('users', 'location_latitude')) {
                $columns[] = 'location_latitude';
            }

            if (Schema::hasColumn('users', 'location_longitude')) {
                $columns[] = 'location_longitude';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};

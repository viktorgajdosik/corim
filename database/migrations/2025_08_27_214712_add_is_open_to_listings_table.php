<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            // Adjust ->after('description') if you want a different position
            $table->boolean('is_open')->default(true)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('is_open');
        });
    }
};

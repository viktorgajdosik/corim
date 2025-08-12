<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Expand the ENUM to include 'modification_requested'
        DB::statement("
            ALTER TABLE tasks
            MODIFY COLUMN status
            ENUM('assigned', 'submitted', 'modification_requested', 'finished')
            NOT NULL
            DEFAULT 'assigned'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Roll back to the previous ENUM without 'modification_requested'
        DB::statement("
            ALTER TABLE tasks
            MODIFY COLUMN status
            ENUM('assigned', 'submitted', 'finished')
            NOT NULL
            DEFAULT 'assigned'
        ");
    }
};

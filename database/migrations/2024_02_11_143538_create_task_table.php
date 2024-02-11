<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users');
            $table->string('name');
            $table->text('description');
            $table->string('file')->nullable();
            $table->enum('status', ['not_assigned', 'assigned', 'submitted', 'pending', 'requested_modification', 'finished'])->default('not_assigned');
            $table->foreignId('listing_id')->constrained()->onDelete('cascade'); // Add this line
            $table->timestamps();
        });

        // Pivot table for task participants
        Schema::create('task_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_participants');
        Schema::dropIfExists('tasks');
    }
};

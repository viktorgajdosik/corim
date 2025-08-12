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
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->string('file')->nullable(); // Optional assignment document
            $table->enum('status', ['not_assigned', 'assigned', 'submitted', 'pending', 'requested_modification', 'finished'])->default('not_assigned');
            $table->timestamps();
        });

        Schema::create('task_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Submission fields by student
            $table->text('result_text')->nullable();
            $table->string('result_file')->nullable();

            // Per-student status for this task
            $table->enum('status', ['assigned', 'submitted', 'modification_requested', 'finished'])->default('assigned');

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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->nullable();          // e.g., 'application.accepted', 'task.updated'
            $table->string('title');                     // short heading
            $table->text('body')->nullable();            // optional descriptive text
            $table->string('url')->nullable();           // internal path to go to when clicked (e.g. /listings/5)
            $table->timestamp('seen_at')->nullable();    // null => unseen
            $table->timestamps();

            $table->index(['user_id', 'seen_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

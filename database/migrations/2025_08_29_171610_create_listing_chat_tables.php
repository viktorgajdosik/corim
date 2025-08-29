<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // sender
            $table->text('body');
            $table->boolean('is_broadcast')->default(true); // true = visible to all audience for the listing
            $table->timestamps();
        });

        Schema::create('chat_message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // recipient
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['message_id','user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_message_recipients');
        Schema::dropIfExists('chat_messages');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // moderation flags
            $table->timestamp('deactivated_at')->nullable()->after('email_verified_at')->index();
            $table->timestamp('banned_at')->nullable()->after('deactivated_at')->index();
            // reason shown to user/admin (255 is fine; use text() if you want longer)
            $table->string('ban_reason', 255)->nullable()->after('banned_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['deactivated_at', 'banned_at', 'ban_reason']);
        });
    }
};

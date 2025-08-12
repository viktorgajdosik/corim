<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->unsignedBigInteger('assigned_user_id')->nullable()->after('author_id');
        $table->text('result_text')->nullable()->after('file');
        $table->string('result_file')->nullable()->after('result_text');
        $table->text('modification_note')->nullable()->after('result_file');

        $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropForeign(['assigned_user_id']);
        $table->dropColumn([
            'assigned_user_id',
            'result_text',
            'result_file',
            'modification_note',
            'status'
        ]);
    });
}

};

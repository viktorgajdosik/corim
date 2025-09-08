<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('institution_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // Organisation name
            $table->string('org_domain');                   // org email domain, e.g. "org.edu"
            $table->string('website_url')->nullable();
            $table->string('contact_email');                // where you'll contact them
            $table->text('message')->nullable();
            $table->string('status')->default('pending');   // pending|approved|declined
            $table->timestamp('decided_at')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['org_domain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_requests');
    }
};

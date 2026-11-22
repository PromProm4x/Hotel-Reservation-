<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->unique();
            $table->string('phone', 50)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('id_document_type', 50)->nullable(); // passport|national_id|driving_licence
            $table->string('id_document_number', 100)->nullable();
            $table->text('notes')->nullable(); // CRM notes: allergies, VIP, etc.
            $table->json('preferences')->nullable(); // e.g. ["high floor", "feather-free pillows"]
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};

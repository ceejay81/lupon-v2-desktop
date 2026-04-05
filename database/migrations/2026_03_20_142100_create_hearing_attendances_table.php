<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hearing_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hearing_id')->constrained()->cascadeOnDelete();
            $table->string('party_type'); // complainant, respondent, witness, lupon_member
            $table->string('name');
            $table->boolean('attended')->default(false);
            $table->string('signature_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hearing_attendances');
    }
};

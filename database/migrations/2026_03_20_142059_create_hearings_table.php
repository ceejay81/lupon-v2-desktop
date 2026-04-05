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
        Schema::create('hearings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lupon_case_id')->constrained()->cascadeOnDelete();
            $table->string('hearing_type'); // mediation, conciliation, arbitration
            $table->datetime('scheduled_at');
            $table->string('location')->default('Barangay Hall');
            $table->text('minutes')->nullable();
            $table->string('outcome')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hearings');
    }
};

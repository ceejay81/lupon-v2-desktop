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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lupon_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hearing_id')->nullable()->constrained()->nullOnDelete();
            $table->string('document_type'); // notice, minutes, attendance, agreement, certificate, other
            $table->string('filename');
            $table->string('file_path');
            $table->integer('file_size')->nullable(); // bytes
            $table->string('mime_type')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

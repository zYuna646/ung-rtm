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
        Schema::create('rtm_rencana_tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->text('rencana_tindak_lanjut');
            $table->string('target_penyelesaian');
            $table->unsignedBigInteger('ami_id')->nullable()->comment('ID of indicator from AMI');
            $table->unsignedBigInteger('survei_id')->nullable()->comment('ID of indicator from Survei');
            $table->unsignedBigInteger('akreditasi_id')->nullable()->comment('ID of indicator from Akreditasi');
            $table->unsignedBigInteger('rtm_id');
            $table->unsignedBigInteger('fakultas_id')->nullable()->comment('Null for university level');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('rtm_id')->references('id')->on('rtms')->onDelete('cascade');
            // Add foreign key for fakultas_id if needed
            // $table->foreign('fakultas_id')->references('id')->on('fakultas')->onDelete('cascade');
            
            // We need unique constraints to avoid duplicates for each type
            $table->unique(['ami_id', 'rtm_id', 'fakultas_id'], 'unique_rtm_ami_fakultas');
            $table->unique(['survei_id', 'rtm_id', 'fakultas_id'], 'unique_rtm_survei_fakultas');
            $table->unique(['akreditasi_id', 'rtm_id', 'fakultas_id'], 'unique_rtm_akreditasi_fakultas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rtm_rencana_tindak_lanjut');
    }
}; 
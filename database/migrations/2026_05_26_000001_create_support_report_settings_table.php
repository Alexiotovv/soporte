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
        Schema::create('support_report_settings', function (Blueprint $table) {
            $table->id();
            $table->string('report_code_suffix')->default('LARG-OTI-UNAP');
            $table->unsignedInteger('sequence_year')->nullable();
            $table->unsignedInteger('last_sequence')->default(0);
            $table->string('recipient_name')->nullable();
            $table->string('recipient_position')->nullable();
            $table->string('sender_prefix')->default('Tco.');
            $table->string('sender_position')->default('Soporte Informatico - OTI');
            $table->string('header_image_path')->nullable();
            $table->string('footer_image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_report_settings');
    }
};

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
        Schema::create('support_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('report_code')->unique();
            $table->unsignedInteger('report_sequence');
            $table->unsignedInteger('report_year');
            $table->date('report_date');
            $table->string('recipient_name');
            $table->string('recipient_position');
            $table->string('sender_name');
            $table->string('sender_position');
            $table->string('subject');
            $table->longText('content');
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
        Schema::dropIfExists('support_reports');
    }
};

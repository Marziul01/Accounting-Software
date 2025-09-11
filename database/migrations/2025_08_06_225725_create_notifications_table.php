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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('sender_name')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('sent_date')->nullable();
            $table->boolean('email_sent')->nullable();
            $table->boolean('sms_sent')->nullable();
            $table->string('occasion_name')->nullable();
            $table->integer('contact_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

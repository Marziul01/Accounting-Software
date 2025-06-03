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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('site_name_bangla')->nullable();
            $table->string('site_email')->unique();
            $table->string('site_phone')->nullable();
            $table->string('site_address')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('site_favicon')->nullable();
            $table->text('site_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};

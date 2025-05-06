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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            // Personal Information
            $table->string('name');
            $table->string('national_id')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_mobile')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_mobile')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('spouse_mobile')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->boolean('sms_enabled')->default(1); // 1 = On, 0 = Off
            $table->boolean('email_enabled')->default(1); // 1 = On, 0 = Off
            $table->string('photo')->nullable(); // file path

            // Asset Info
            $table->integer('asset_category_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('entry_date')->nullable();
            $table->boolean('send_sms')->default(0);
            $table->boolean('send_email')->default(0);

            // Optional: Link to Contact
            $table->integer('contact_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

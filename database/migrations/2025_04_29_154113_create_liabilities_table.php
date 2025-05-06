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
        Schema::create('liabilities', function (Blueprint $table) {
            $table->id();
            // Personal details
            $table->string('name');
            $table->string('nid')->nullable();
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
            $table->boolean('sms_status')->default(1); // 1 = on, 0 = off
            $table->boolean('email_status')->default(1); // 1 = on, 0 = off
            $table->string('photo')->nullable(); // path to uploaded image

            // Relationships
            $table->integer('contact_id')->nullable();
            $table->integer('liability_category_id')->nullable();

            // Liability Info
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('date')->nullable();
            $table->boolean('send_sms')->default(0);
            $table->boolean('send_email')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liabilities');
    }
};

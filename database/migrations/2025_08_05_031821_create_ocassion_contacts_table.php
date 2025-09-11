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
        Schema::create('ocassion_contacts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ocassion_id')->nullable();
            $table->bigInteger('contact_id')->nullable();
            $table->integer('sented')->default(1);
            $table->year('next_send')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocassion_contacts');
    }
};

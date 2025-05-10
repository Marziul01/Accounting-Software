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
        Schema::create('user_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('admin_panel')->default(1); // 1 for user, 2 for admin
            $table->tinyInteger('sms_and_email')->default(1); // 1 for user, 2 for admin
            $table->tinyInteger('contact')->default(1); // 1 for user, 2 for admin
            $table->tinyInteger('income')->default(1); // 1 for user, 2 for admin
            $table->tinyInteger('expense')->default(1); // 1 for user, 2 for admin
            $table->tinyInteger('investment')->default(1); // 1 for user, 2 for admin
            $table->tinyInteger('asset')->default(1); // 1 for user, 2 for admin
            $table->tinyInteger('liability')->default(1); // 1 for user, 2 for admin
            $table->tinyInteger('bankbook')->default(1); // 1 for user, 2 for admin
            $table->tinyInteger('accounts')->default(1); // 1 for user, 2 for admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accesses');
    }
};

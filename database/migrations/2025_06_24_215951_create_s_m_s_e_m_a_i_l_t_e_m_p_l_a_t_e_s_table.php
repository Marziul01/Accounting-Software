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
        Schema::create('s_m_s_e_m_a_i_l_t_e_m_p_l_a_t_e_s', function (Blueprint $table) {
            $table->id();
            $table->text('contact_ids')->nullable();
            $table->text('occassion')->nullable();
            $table->date('custom_date')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_m_s_e_m_a_i_l_t_e_m_p_l_a_t_e_s');
    }
};

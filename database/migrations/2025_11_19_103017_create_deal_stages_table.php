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
        Schema::create('deal_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'Lead', 'Contacted', 'Proposal', 'Negotiation', 'Won', 'Lost'
            $table->integer('order')->default(0);
            $table->string('color')->default('#3B82F6'); // Tailwind blue-500
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_stages');
    }
};

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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('deal_stage_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 12, 2);
            $table->date('closing_date')->nullable();
            $table->integer('probability')->default(0); // 0-100
            $table->foreignId('assigned_to_id')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};

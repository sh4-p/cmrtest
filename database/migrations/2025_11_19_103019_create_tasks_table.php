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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('status')->default('Pending'); // 'Pending', 'In Progress', 'Completed'
            $table->string('priority')->default('Medium'); // 'Low', 'Medium', 'High', 'Urgent'
            $table->foreignId('assigned_to_id')->constrained('users')->onDelete('cascade');
            $table->morphs('related_to'); // Polymorphic: related_to_type, related_to_id
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

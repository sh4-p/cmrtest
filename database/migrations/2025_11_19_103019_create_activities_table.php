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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->string('type'); // 'Call', 'Meeting', 'Email', 'Note', 'Task Completed'
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('subject'); // subject_type, subject_id (Contact, Deal, Lead)
            $table->timestamp('activity_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

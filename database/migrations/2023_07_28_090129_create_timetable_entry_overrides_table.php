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
        Schema::create('timetable_entry_overrides', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('hour', 8, 1);
            $table->string('note')->nullable();
            $table->foreignId('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
            $table->foreignId('subject_teacher_id')->nullable()->references('id')->on('subject_teachers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_entry_overrides');
    }
};

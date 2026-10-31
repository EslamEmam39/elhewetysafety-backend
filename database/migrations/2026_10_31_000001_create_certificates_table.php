<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('issued_by_user_id')->constrained('users')->restrictOnDelete();
            $table->string('recipient_full_name_en');
            $table->string('recipient_full_name_ar');
            $table->string('course_title');
            $table->string('pdf_disk', 32);
            $table->string('pdf_storage_key', 1024);
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index('issued_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};

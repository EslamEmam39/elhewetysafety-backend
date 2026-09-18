<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('course_category_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('level', 20)->default('beginner');
            $table->renameColumn('thumbnail', 'thumbnail_storage_key');
            $table->index(['course_category_id', 'status']);
            $table->index(['instructor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['course_category_id', 'status']);
            $table->dropIndex(['instructor_id', 'status']);
            $table->dropConstrainedForeignId('course_category_id');
            $table->dropConstrainedForeignId('instructor_id');
            $table->dropColumn('level');
            $table->renameColumn('thumbnail_storage_key', 'thumbnail');
        });
    }
};

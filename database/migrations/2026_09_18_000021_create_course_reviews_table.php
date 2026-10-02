<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('rating');
            $table->text('review_text')->nullable();
            $table->timestamp('hidden_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
            $table->index(['course_id', 'hidden_at']);
        });

        DB::statement('ALTER TABLE course_reviews ADD CONSTRAINT course_reviews_rating_check CHECK (rating BETWEEN 1 AND 5)');
    }

    public function down(): void
    {
        Schema::dropIfExists('course_reviews');
    }
};

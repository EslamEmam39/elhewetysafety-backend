<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', fn (Blueprint $table) => $table->string('code')->nullable()->unique());
        DB::table('lessons')->orderBy('id')->each(fn (object $lesson) => DB::table('lessons')
            ->where('id', $lesson->id)->update(['code' => 'lesson-'.$lesson->id]));
        Schema::table('lessons', fn (Blueprint $table) => $table->string('code')->nullable(false)->change());
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};

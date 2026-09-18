<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chapters', fn(Blueprint $table) => $table->string('code')->nullable()->unique());
        DB::table('chapters')->orderBy('id')->each(fn(object $chapter) => DB::table('chapters')
            ->where('id', $chapter->id)->update(['code' => 'chapter-' . $chapter->id]));
        Schema::table('chapters', fn(Blueprint $table) => $table->string('code')->nullable(false)->change());
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};

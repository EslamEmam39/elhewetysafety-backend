<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->renameColumn('video_s3_key', 'video_storage_key');
            $table->string('video_disk', 64)->nullable()->after('video_storage_key');
            $table->string('video_mime_type', 100)->nullable()->after('video_disk');
            $table->unsignedBigInteger('video_size_bytes')->nullable()->after('video_mime_type');
        });

        DB::table('lessons')
            ->whereNotNull('video_storage_key')
            ->whereNull('video_disk')
            ->update(['video_disk' => 's3']);
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['video_disk', 'video_mime_type', 'video_size_bytes']);
            $table->renameColumn('video_storage_key', 'video_s3_key');
        });
    }
};

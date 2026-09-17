<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Keep the numeric primary key for existing foreign keys; UUID is the public identifier.
            $table->uuid('uuid')->nullable()->unique();
            $table->string('username')->nullable()->unique();
            $table->string('phone', 32)->nullable()->unique();
            $table->string('full_name_en')->nullable();
            $table->string('full_name_ar')->nullable();
            $table->boolean('is_banned')->default(false);
            $table->text('ban_reason')->nullable();
        });

        // Existing accounts have no UUID yet. Generate one in PHP so PostgreSQL extensions
        // such as pgcrypto are not a migration prerequisite.
        DB::table('users')->orderBy('id')->each(function (object $user): void {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'uuid' => (string) Str::uuid(),
                    'full_name_en' => $user->name,
                ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        DB::table('users')->orderBy('id')->each(function (object $user): void {
            DB::table('users')->where('id', $user->id)->update([
                'name' => $user->full_name_en,
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->dropUnique(['uuid']);
            $table->dropUnique(['username']);
            $table->dropUnique(['phone']);
            $table->dropColumn([
                'uuid',
                'username',
                'phone',
                'full_name_en',
                'full_name_ar',
                'is_banned',
                'ban_reason',
            ]);
        });
    }
};

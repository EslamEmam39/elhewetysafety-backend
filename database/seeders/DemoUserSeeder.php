<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Student;
use App\Models\TargetedProfessionalField;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment('local')) {
            $this->command?->warn('Demo users were not seeded outside the local environment.');

            return;
        }

        $professionalField = TargetedProfessionalField::query()
            ->where('code', 'nebosh')
            ->firstOrFail();
        $cairo = City::query()->where('code', 'eg-cairo')->firstOrFail();
        $password = Hash::make('password');

        DB::transaction(function () use ($professionalField, $cairo, $password): void {
            $admin = $this->upsertUser([
                'username' => 'admin',
                'phone' => '+201000000001',
                'full_name_en' => 'System Administrator',
                'full_name_ar' => 'مدير النظام',
                'email' => 'admin@elhewety.test',
                'email_verified_at' => now(),
                'password' => $password,
                'is_banned' => false,
                'ban_reason' => null,
                'targeted_professional_field_id' => null,
            ]);
            $admin->syncRoles(['admin']);

            $student = $this->upsertUser([
                'username' => 'student',
                'phone' => '+201000000002',
                'full_name_en' => 'Demo Student',
                'full_name_ar' => 'طالب تجريبي',
                'email' => 'student@elhewety.test',
                'email_verified_at' => now(),
                'password' => $password,
                'is_banned' => false,
                'ban_reason' => null,
                'targeted_professional_field_id' => $professionalField->id,
            ]);
            $student->syncRoles(['student']);
            Student::query()->updateOrCreate(
                ['user_id' => $student->id],
                [
                    'city_id' => $cairo->id,
                    'avatar_storage_key' => null,
                    'job_title' => 'Safety Officer',
                    'company' => 'Elhewety Safety Demo',
                    'bio' => 'Local account for testing student profile and authenticated APIs.',
                ]
            );

            $bannedStudent = $this->upsertUser([
                'username' => 'banned.student',
                'phone' => '+201000000003',
                'full_name_en' => 'Banned Student',
                'full_name_ar' => 'طالب محظور',
                'email' => 'banned@elhewety.test',
                'email_verified_at' => now(),
                'password' => $password,
                'is_banned' => true,
                'ban_reason' => 'Demo account for testing the ban flow.',
                'targeted_professional_field_id' => $professionalField->id,
            ]);
            $bannedStudent->syncRoles(['student']);
            Student::query()->updateOrCreate(['user_id' => $bannedStudent->id], ['city_id' => $cairo->id]);

            $unverifiedStudent = $this->upsertUser([
                'username' => 'unverified.student',
                'phone' => '+201000000004',
                'full_name_en' => 'Unverified Student',
                'full_name_ar' => 'طالب غير موثق',
                'email' => 'unverified@elhewety.test',
                'email_verified_at' => null,
                'password' => $password,
                'is_banned' => false,
                'ban_reason' => null,
                'targeted_professional_field_id' => $professionalField->id,
            ]);
            $unverifiedStudent->syncRoles(['student']);
            Student::query()->updateOrCreate(['user_id' => $unverifiedStudent->id], ['city_id' => $cairo->id]);
        });
    }

    private function upsertUser(array $attributes): User
    {
        $user = User::query()->firstOrNew(['email' => $attributes['email']]);
        $emailVerifiedAt = $attributes['email_verified_at'];
        unset($attributes['email_verified_at']);

        if (blank($user->uuid)) {
            $user->uuid = (string) Str::uuid();
        }

        $user->fill($attributes);
        $user->email_verified_at = $emailVerifiedAt;
        $user->save();

        return $user;
    }
}

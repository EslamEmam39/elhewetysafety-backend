<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $devices = [
            ['email' => 'admin@elhewety.test', 'uuid' => '10000000-0000-4000-8000-000000000001', 'name' => 'Admin Postman', 'platform' => 'postman'],
            ['email' => 'student@elhewety.test', 'uuid' => '20000000-0000-4000-8000-000000000001', 'name' => 'Student Postman', 'platform' => 'postman'],
        ];

        foreach ($devices as $data) {
            $user = User::query()->where('email', $data['email'])->firstOrFail();
            Device::query()->updateOrCreate(
                ['uuid' => $data['uuid']],
                ['user_id' => $user->id, 'name' => $data['name'], 'platform' => $data['platform'], 'last_activity_at' => now(), 'revoked_at' => null]
            );
        }
    }
}

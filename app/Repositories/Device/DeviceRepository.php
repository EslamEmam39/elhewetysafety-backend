<?php

namespace App\Repositories\Device;

use App\Exceptions\Device\DeviceIdentifierInUseException;
use App\Models\Device;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DeviceRepository
{
    public function registerForLogin(User $user, array $data): Device
    {
        $device = Device::query()->where('uuid', $data['device_uuid'])->lockForUpdate()->first();

        if ($device && $device->user_id !== $user->id) {
            throw new DeviceIdentifierInUseException;
        }

        if (!$device) {
            $device = new Device(['uuid' => $data['device_uuid'], 'user_id' => $user->id]);
        }

        $device->fill([
            'name' => $data['device_name'],
            'platform' => $data['device_platform'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'last_activity_at' => now(),
            'revoked_at' => null,
        ])->save();

        return $device;
    }

    public function attachToken(Device $device, PersonalAccessToken $token): void
    {
        $token->device()->associate($device);
        $token->save();
    }

    public function forUser(User $user): Collection
    {
        return Device::query()->whereBelongsTo($user)->latest('last_activity_at')->get();
    }

    public function lockOwned(User $user, Device $device): ?Device
    {
        return Device::query()->whereKey($device->id)->whereBelongsTo($user)->lockForUpdate()->first();
    }

    public function revoke(Device $device): Device
    {
        return DB::transaction(function () use ($device): Device {
            $locked = Device::query()->whereKey($device->id)->lockForUpdate()->firstOrFail();
            $locked->tokens()->delete();
            $locked->update(['revoked_at' => now()]);

            return $locked->refresh();
        });
    }

    public function touchFromToken(?int $deviceId, ?string $ipAddress, ?string $userAgent): void
    {
        if (!$deviceId) {
            return;
        }

        Device::query()->whereKey($deviceId)->whereNull('revoked_at')->update([
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'last_activity_at' => now(),
        ]);
    }
}

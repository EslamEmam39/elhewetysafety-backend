<?php

namespace App\Services\Device;

use App\Models\Device;
use App\Models\User;
use App\Repositories\Device\DeviceRepository;
use Illuminate\Database\Eloquent\Collection;

class DeviceService
{
    public function __construct(private readonly DeviceRepository $repository) {}

    public function forUser(User $user): Collection
    {
        return $this->repository->forUser($user);
    }

    public function revokeOwned(User $user, Device $device): Device
    {
        $owned = $this->repository->lockOwned($user, $device) ?? abort(404);

        return $this->repository->revoke($owned);
    }

    public function forAdmin(User $user): Collection
    {
        return $this->repository->forUser($user);
    }

    public function revokeAny(Device $device): Device
    {
        return $this->repository->revoke($device);
    }
}

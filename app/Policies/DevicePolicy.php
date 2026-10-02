<?php
namespace App\Policies;
use App\Models\Device;
use App\Models\User;
class DevicePolicy { public function viewAny(User $user): bool { return $user->checkPermissionTo('devices.view'); } public function manage(User $user, Device $device): bool { return $user->checkPermissionTo('devices.manage'); } }

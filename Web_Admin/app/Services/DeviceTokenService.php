<?php

namespace App\Services;

use App\Models\AdminDeviceKey;
use App\Models\DeviceKey;
use App\Models\DriverDeviceKey;
use App\Models\User;

class DeviceTokenService
{
    public function all(): array
    {
        $tokens = [];

        $customerTokens = DeviceKey::query()
            ->join('customers', 'customers.id', '=', 'device_keys.customer_id')
            ->join('users', 'users.id', '=', 'customers.user_id')
            ->select([
                'device_keys.key as token',
                'device_keys.device_type',
                'users.id as user_id',
                'users.first_name',
                'users.last_name',
            ])
            ->get();

        foreach ($customerTokens as $row) {
            $tokens[] = [
                'role' => 'customer',
                'token' => $row->token,
                'user_id' => $row->user_id,
                'name' => trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')),
                'device_type' => $row->device_type,
            ];
        }

        $driverTokens = DriverDeviceKey::query()
            ->join('drivers', 'drivers.id', '=', 'driver_device_keys.driver_id')
            ->join('users', 'users.id', '=', 'drivers.user_id')
            ->select([
                'driver_device_keys.key as token',
                'users.id as user_id',
                'users.first_name',
                'users.last_name',
            ])
            ->get();

        foreach ($driverTokens as $row) {
            $tokens[] = [
                'role' => 'driver',
                'token' => $row->token,
                'user_id' => $row->user_id,
                'name' => trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')),
                'device_type' => null,
            ];
        }

        $adminTokens = AdminDeviceKey::query()
            ->join('users', 'users.id', '=', 'admin_device_keys.user_id')
            ->select([
                'admin_device_keys.key as token',
                'users.id as user_id',
                'users.first_name',
                'users.last_name',
            ])
            ->get();

        foreach ($adminTokens as $row) {
            $tokens[] = [
                'role' => 'admin',
                'token' => $row->token,
                'user_id' => $row->user_id,
                'name' => trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')),
                'device_type' => null,
            ];
        }

        return array_values(array_unique($tokens, SORT_REGULAR));
    }

    public function firstForRole(string $role): ?string
    {
        foreach ($this->all() as $token) {
            if ($token['role'] === $role && !empty($token['token'])) {
                return $token['token'];
            }
        }

        return null;
    }

    public function roleCounts(): array
    {
        $counts = ['customer' => 0, 'driver' => 0, 'admin' => 0];
        foreach ($this->all() as $token) {
            $counts[$token['role']] = ($counts[$token['role']] ?? 0) + 1;
        }

        return $counts;
    }
}

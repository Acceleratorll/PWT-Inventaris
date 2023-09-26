<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToModel, WithHeadingRow
{
    protected $user;

    public function model(array $row)
    {
        return new User([
            'role_id' => $this->roles($row['role']),
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
            'updated_at' => $row['di_update'],
            'created_at' => $row['di_buat'],
        ]);
    }

    private function roles($data)
    {
        $roleMapping = [
            'Staff' => 1,
            'Kadev' => 2,
        ];

        return $roleMapping[$data] ?? 1;
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = Role::create(['name' => 'staff']);
        $logistik = Role::create(['name' => 'logistik']);
        $ppic = Role::create(['name' => 'ppic']);

        $userStaff = User::find(1);
        $userLogistik = User::find(2);
        $userPpic = User::find(3);

        // Create permissions
        $permissionNotaDinas = Permission::create(['name' => 'manage nota dinas']);

        $permissionReadProduct = Permission::create(['name' => 'read product']);
        $permissionCreateProduct = Permission::create(['name' => 'create product']);
        $permissionUpdateProduct = Permission::create(['name' => 'update product']);
        $permissionDeleteProduct = Permission::create(['name' => 'delete product']);
        $permissionCRUDProduct = Permission::create(['name' => 'crud product']);

        // Assign permissions to roles
        $ppic->givePermissionTo($permissionNotaDinas);
        $staff->givePermissionTo($permissionCRUDProduct);
        $logistik->givePermissionTo($permissionCRUDProduct);
        $ppic->givePermissionTo($permissionCRUDProduct);

        // // Assign roles to permissions
        // $permissionCRUDProduct->assignRole($ppic);
        // $permissionCRUDProduct->assignRole($staff);
        // $permissionCRUDProduct->assignRole($logistik);

        // Assign users to roles
        $userStaff->assignRole($staff);
        $userLogistik->assignRole($logistik);
        $userPpic->assignRole($ppic);
    }
}

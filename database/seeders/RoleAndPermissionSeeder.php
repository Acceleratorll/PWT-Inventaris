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
        $permissionAccessCategory = Permission::create(['name' => 'access category']);
        $permissionAccessProduct = Permission::create(['name' => 'access product']);
        $permissionAccessTransaction = Permission::create(['name' => 'access Transaction']);
        $permissionAccessLocation = Permission::create(['name' => 'access location']);
        $permissionAccessType = Permission::create(['name' => 'access type']);
        $permissionAccessRpp = Permission::create(['name' => 'access rpp']);
        $permissionAccessNotaDinas = Permission::create(['name' => 'access nota dinas']);
        $permissionLoggedIn = Permission::create(['name' => 'logged in']);

        $permissionViewLocation = Permission::create(['name' => 'view location']);
        $permissionCreateLocation = Permission::create(['name' => 'create location']);
        $permissionUpdateLocation = Permission::create(['name' => 'update location']);
        $permissionDeleteLocation = Permission::create(['name' => 'delete location']);

        $permissionViewQualifier = Permission::create(['name' => 'view qualifier']);
        $permissionCreateQualifier = Permission::create(['name' => 'create qualifier']);
        $permissionUpdateQualifier = Permission::create(['name' => 'update qualifier']);
        $permissionDeleteQualifier = Permission::create(['name' => 'delete qualifier']);

        $permissionViewCategory = Permission::create(['name' => 'view category']);
        $permissionCreateCategory = Permission::create(['name' => 'create category']);
        $permissionUpdateCategory = Permission::create(['name' => 'update category']);
        $permissionDeleteCategory = Permission::create(['name' => 'delete category']);

        $permissionViewCustomer = Permission::create(['name' => 'view customer']);
        $permissionCreateCustomer = Permission::create(['name' => 'create customer']);
        $permissionUpdateCustomer = Permission::create(['name' => 'update customer']);
        $permissionDeleteCustomer = Permission::create(['name' => 'delete customer']);

        $permissionViewMaterial = Permission::create(['name' => 'view material']);
        $permissionCreateMaterial = Permission::create(['name' => 'create material']);
        $permissionUpdateMaterial = Permission::create(['name' => 'update material']);
        $permissionDeleteMaterial = Permission::create(['name' => 'delete material']);

        $permissionViewType = Permission::create(['name' => 'view type']);
        $permissionCreateType = Permission::create(['name' => 'create type']);
        $permissionUpdateType = Permission::create(['name' => 'update type']);
        $permissionDeleteType = Permission::create(['name' => 'delete type']);

        $permissionViewSupplier = Permission::create(['name' => 'view supplier']);
        $permissionCreateSupplier = Permission::create(['name' => 'create supplier']);
        $permissionUpdateSupplier = Permission::create(['name' => 'update supplier']);
        $permissionDeleteSupplier = Permission::create(['name' => 'delete supplier']);

        $permissionViewUser = Permission::create(['name' => 'view user']);
        $permissionCreateUser = Permission::create(['name' => 'create user']);
        $permissionUpdateUser = Permission::create(['name' => 'update user']);
        $permissionDeleteUser = Permission::create(['name' => 'delete user']);

        $permissionViewNotaDinas = Permission::create(['name' => 'view nota dinas']);
        $permissionCreateNotaDinas = Permission::create(['name' => 'create nota dinas']);
        $permissionUpdateNotaDinas = Permission::create(['name' => 'update nota dinas']);
        $permissionDeleteNotaDinas = Permission::create(['name' => 'delete nota dinas']);

        $permissionViewProduct = Permission::create(['name' => 'view product']);
        $permissionCreateProduct = Permission::create(['name' => 'create product']);
        $permissionUpdateProduct = Permission::create(['name' => 'update product']);
        $permissionDeleteProduct = Permission::create(['name' => 'delete product']);

        $permissionViewProductLocation = Permission::create(['name' => 'view product location']);
        $permissionCreateProductLocation = Permission::create(['name' => 'create product location']);
        $permissionUpdateProductLocation = Permission::create(['name' => 'update product location']);
        $permissionDeleteProductLocation = Permission::create(['name' => 'delete product location']);

        $permissionViewRpp = Permission::create(['name' => 'view rpp']);
        $permissionCreateRpp = Permission::create(['name' => 'create rpp']);
        $permissionUpdateRpp = Permission::create(['name' => 'update rpp']);
        $permissionDeleteRpp = Permission::create(['name' => 'delete rpp']);

        $permissionViewTransaction = Permission::create(['name' => 'view transaction']);
        $permissionCreateTransaction = Permission::create(['name' => 'create transaction']);
        $permissionUpdateTransaction = Permission::create(['name' => 'update transaction']);
        $permissionDeleteTransaction = Permission::create(['name' => 'delete transaction']);

        // Assign permissions to roles
        $logistik->givePermissionTo($permissionLoggedIn);
        $logistik->givePermissionTo($permissionAccessProduct);
        $logistik->givePermissionTo($permissionAccessNotaDinas);
        $logistik->givePermissionTo($permissionAccessRpp);
        $logistik->givePermissionTo($permissionAccessType);
        $logistik->givePermissionTo($permissionAccessTransaction);
        $logistik->givePermissionTo($permissionViewUser);
        $logistik->givePermissionTo($permissionViewProduct);
        $logistik->givePermissionTo($permissionViewNotaDinas);
        $logistik->givePermissionTo($permissionViewRpp);
        $logistik->givePermissionTo($permissionViewType);
        $logistik->givePermissionTo($permissionCreateType);
        $logistik->givePermissionTo($permissionUpdateType);
        $logistik->givePermissionTo($permissionDeleteType);
        $logistik->givePermissionTo($permissionViewSupplier);
        $logistik->givePermissionTo($permissionCreateSupplier);
        $logistik->givePermissionTo($permissionUpdateSupplier);
        $logistik->givePermissionTo($permissionDeleteSupplier);
        $logistik->givePermissionTo($permissionViewTransaction);
        $logistik->givePermissionTo($permissionCreateTransaction);
        $logistik->givePermissionTo($permissionUpdateTransaction);
        $logistik->givePermissionTo($permissionDeleteTransaction);

        $ppic->givePermissionTo($permissionLoggedIn);
        $ppic->givePermissionTo($permissionAccessNotaDinas);
        $ppic->givePermissionTo($permissionAccessProduct);
        $ppic->givePermissionTo($permissionAccessRpp);
        $ppic->givePermissionTo($permissionAccessTransaction);
        $ppic->givePermissionTo($permissionViewUser);
        $ppic->givePermissionTo($permissionViewNotaDinas);
        $ppic->givePermissionTo($permissionCreateNotaDinas);
        $ppic->givePermissionTo($permissionUpdateNotaDinas);
        $ppic->givePermissionTo($permissionDeleteNotaDinas);
        $ppic->givePermissionTo($permissionViewProduct);
        $ppic->givePermissionTo($permissionViewCustomer);
        $ppic->givePermissionTo($permissionCreateCustomer);
        $ppic->givePermissionTo($permissionUpdateCustomer);
        $ppic->givePermissionTo($permissionDeleteCustomer);
        $ppic->givePermissionTo($permissionViewRpp);
        $ppic->givePermissionTo($permissionCreateRpp);
        $ppic->givePermissionTo($permissionUpdateRpp);
        $ppic->givePermissionTo($permissionDeleteRpp);
        $ppic->givePermissionTo($permissionViewTransaction);

        $staff->givePermissionTo($permissionLoggedIn);
        $staff->givePermissionTo($permissionAccessType);
        $staff->givePermissionTo($permissionAccessProduct);
        $staff->givePermissionTo($permissionAccessTransaction);
        $staff->givePermissionTo($permissionAccessCategory);
        $staff->givePermissionTo($permissionAccessLocation);
        $staff->givePermissionTo($permissionViewUser);
        $staff->givePermissionTo($permissionViewRpp);
        $staff->givePermissionTo($permissionViewType);
        $staff->givePermissionTo($permissionCreateType);
        $staff->givePermissionTo($permissionUpdateType);
        $staff->givePermissionTo($permissionDeleteType);
        $staff->givePermissionTo($permissionViewMaterial);
        $staff->givePermissionTo($permissionCreateMaterial);
        $staff->givePermissionTo($permissionUpdateMaterial);
        $staff->givePermissionTo($permissionDeleteMaterial);
        $staff->givePermissionTo($permissionViewProduct);
        $staff->givePermissionTo($permissionCreateProduct);
        $staff->givePermissionTo($permissionUpdateProduct);
        $staff->givePermissionTo($permissionDeleteProduct);
        $staff->givePermissionTo($permissionViewTransaction);
        $staff->givePermissionTo($permissionCreateTransaction);
        $staff->givePermissionTo($permissionUpdateTransaction);
        $staff->givePermissionTo($permissionDeleteTransaction);
        $staff->givePermissionTo($permissionViewQualifier);
        $staff->givePermissionTo($permissionCreateQualifier);
        $staff->givePermissionTo($permissionUpdateQualifier);
        $staff->givePermissionTo($permissionDeleteQualifier);
        $staff->givePermissionTo($permissionViewCategory);
        $staff->givePermissionTo($permissionCreateCategory);
        $staff->givePermissionTo($permissionUpdateCategory);
        $staff->givePermissionTo($permissionDeleteCategory);
        $staff->givePermissionTo($permissionViewLocation);
        $staff->givePermissionTo($permissionCreateLocation);
        $staff->givePermissionTo($permissionUpdateLocation);
        $staff->givePermissionTo($permissionDeleteLocation);
        $staff->givePermissionTo($permissionViewProductLocation);
        $staff->givePermissionTo($permissionCreateProductLocation);
        $staff->givePermissionTo($permissionUpdateProductLocation);
        $staff->givePermissionTo($permissionDeleteProductLocation);

        // Assign users to roles
        $userStaff->assignRole($staff);
        $userLogistik->assignRole($logistik);
        $userPpic->assignRole($ppic);
    }
}

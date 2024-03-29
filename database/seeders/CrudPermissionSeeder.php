<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CrudPermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->createScopePermissions('categories', ['create', 'read', 'update', 'delete']);
    $this->createScopePermissions('locations', ['create', 'read', 'update', 'delete']);
    $this->createScopePermissions('events', ['create', 'read', 'update', 'delete','cancel']);
    $this->createScopePermissions('uploads', ['create', 'read', 'update', 'delete']);
    $adminRole = Role::where('name', 'admin')->first();
    $userRole = Role::where('name', 'user')->first();
    $this->assignScopePermissionsToRole($adminRole, 'categories', ['create', 'read', 'update', 'delete']);
    $this->assignScopePermissionsToRole($adminRole, 'events', ['create', 'read','cancel']);
    $this->assignScopePermissionsToRole($adminRole, 'locations', ['create', 'read','update', 'delete']);
    $this->assignScopePermissionsToRole($adminRole, 'uploads', ['create', 'read', 'update', 'delete']);
    $this->assignScopePermissionsToRole($userRole, 'uploads', ['create', 'read', 'update', 'delete']);
    $this->assignScopePermissionsToRole($userRole, 'categories', ['read']);
    $this->assignScopePermissionsToRole($userRole, 'events', ['read', 'create']);
    $this->assignScopePermissionsToRole($userRole, 'locations', ['read', 'create','update']);
    /*
      Here, include project specific permissions. E.G.:
      $this->createScopePermissions('interests', ['create', 'read', 'update', 'delete', 'import', 'export']);
      $this->createScopePermissions('games', ['create', 'read', 'read_own', 'update', 'delete']);

      $adminRole = Role::where('name', 'admin')->first();
      $this->assignScopePermissionsToRole($adminRole, 'interests', ['create', 'read', 'update', 'delete', 'import', 'export']);
      $this->assignScopePermissionsToRole($adminRole, 'games', ['create', 'read', 'read_own', 'update', 'delete']);

      $advertiserRole = Role::where('name', 'advertiser')->first();
      $this->assignScopePermissionsToRole($advertiserRole, 'interests', ['read']);
      $this->assignScopePermissionsToRole($advertiserRole, 'games', ['create', 'read_own']);
    */
  }

  public function createRole(string $name): Role
  {
    $role = Role::firstOrCreate(['name' => $name]);
    return $role;
  }
  public function createScopePermissions(string $scope, array $permissions): void
  {
    foreach ($permissions as $permission) {
      Permission::firstOrCreate(['name' => $scope . '.' . $permission]);
    }
  }
  public function assignScopePermissionsToRole(Role $role, string $scope, array $permissions): void
  {
    foreach ($permissions as $permission) {
      $permissionName = $scope . '.' . $permission;

      if (!$role->hasPermission($permissionName)) {
        $role->givePermission($permissionName);
      }
    }
  }
}

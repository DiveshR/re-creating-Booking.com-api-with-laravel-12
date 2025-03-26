<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $getAllRoles = Role::all()->keyBy('id');
        
        $permissions = [
            'properties-manage' => [Role::ROLE_OWNER],
            'booking-manage' => [Role::ROLE_USER]
        ];

        foreach ($permissions as $key =>$roles){
            $permission = Permission::create(['name' => $key]);

            foreach ($roles as $role){
                $getAllRoles[$role]->permissions()->attach($permission->id);
            }
        }
    }
}

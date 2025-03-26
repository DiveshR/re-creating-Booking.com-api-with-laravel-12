<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

### 1. Roles and permissions

We will have three roles, at least:

Administrator
Property owner
Simple User

Roles and Permissions in the DB
```
php artisan make:model Role -ms

```

Next, each role will have multiple Permissions. So let's store them in the database, too. The DB structure is identical to the Role:

```
php artisan make:model Permission -m
```

Now, the relationship. It should be a many-to-many, because both each role may have many permissions, and also each permission may belong to many roles.

```
php artisan make:migration create_permission_role_table
```

Ok, great, we have the relationship between roles and permissions. Now, how do we assign roles or permissions to Users?


##### User: One Role or Multiple Roles?

Typically, there are two layers of managing permissions:

Admin adds the permissions and then specifies which roles have certain permissions
For users, the admin/system assigns the ROLES to them, which in itself includes the permissions
In other words, we don't assign permissions to the users, we assign only the roles.

```
php artisan make:migration add_role_id_to_users_table
```

```
php artisan make:seeder AdminUserSeeder
```

```
php artisan migrate --seed
```

#### Registration API: Assign Permissions

```
php artisan make:controller Api/V1/Auth/RegisterController --invokable
```

#### Permissions
```
php artisan make:seeder PermissionSeeder
```

database/seeders/PermissionSeeder.php:

```
namespace Database\Seeders;
 
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allRoles = Role::all()->keyBy('id');
 
        $permissions = [
            'properties-manage' => [Role::ROLE_OWNER],
            'bookings-manage' => [Role::ROLE_USER],
        ];
 
        foreach ($permissions as $key => $roles) {
            $permission = Permission::create(['name' => $key]);
            foreach ($roles as $role) {
                $allRoles[$role]->permissions()->attach($permission->id);
            }
        }
    }
}
```

```
php artisan db:seed --class=PermissionSeeder
```

```
php artisan make:controller Api/V1/Owner/PropertyController
php artisan make:controller Api/V1/User/BookingController
```

We will generate a Middleware specifically for that and enable that Middleware to run on every API request.


```
php artisan make:middleware GateDefineMiddleware
```


### Alternative: Spatie Permission Package

```
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Then, before we run the migrations, we need to remove our own ones. We don't need our own DB structure for roles/permissions, as the package will have their own migrations for this.

So we delete the migrations for:

- migration for roles
- migration for permissions
- migration for pivot permission_role table
- column role_id from users migrations

```
php artisan migrate
```

Then we run php artisan migrate to get the package tables into our DB.

Next, the Models. Again, we don't need our own models, because the package has its own Role and Permission models, with the same field "name" in the structure.


We need to add a Trait in the User model:

app/Models/User.php:

```
use Spatie\Permission\Traits\HasRoles;
use HasApiTokens, HasFactory, Notifiable, HasRoles;

```

database/seeders/AdminUserSeeder.php:
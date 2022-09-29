<?php

namespace App\Services\Installation;

use App\Entities\Permission;
use App\Entities\Role;
use App\Entities\User;
use App\Services\Installation\Events\ApplicationWasInstalled;
use Closure;
use Illuminate\Validation\ValidationException;

/**
 * Class InstallAppHandler.
 */
class InstallAppHandler
{
    /**
     * @var array|\Illuminate\Support\Collection
     */
    public $roles = [
        ['name' => 'Administrator'],
    ];

    /**
     * @var array|\Illuminate\Support\Collection
     */
    public $permissions = [
        'users' => [
            ['name' => 'List users'],
            ['name' => 'Create users'],
            ['name' => 'Delete users'],
            ['name' => 'Update users'],
        ],
        'roles' => [
            ['name' => 'List roles'],
            ['name' => 'Create roles'],
            ['name' => 'Delete roles'],
            ['name' => 'Update roles'],
        ],
        'permissions' => [
            ['name' => 'List permissions'],
        ],
      
       'user_profiles' => [
            ['name' => 'List user profiles'],
            ['name' => 'Create user profiles'],
            ['name' => 'Delete user profiles'],
            ['name' => 'Update user profiles'],
        ],
      
        'user_address' => [
            ['name' => 'List user address'],
            ['name' => 'Create user address'],
            ['name' => 'Delete user address'],
            ['name' => 'Update user address'],
        ],

        'config_status' =>
        [
            ['name' => 'List config status'],
            ['name' => 'Create config status'],
            ['name' => 'Delete config status'],
            ['name' => 'Update config status'],
        ],

       
        'config_payment_status' =>
        [
            ['name' => 'List config payment status'],
            ['name' => 'Create config payment status'],
            ['name' => 'Delete config payment status'],
            ['name' => 'Update config payment status'],
        ],

        
        'config_payment_mode' =>
        [
            ['name' => 'List config payment mode'],
            ['name' => 'Create config payment mode'],
            ['name' => 'Delete config payment mode'],
            ['name' => 'Update config payment mode'],
        ],

        'news' =>
        [
            ['name' => 'List news'],
            ['name' => 'Create news'],
            ['name' => 'Delete news'],
            ['name' => 'Update news'],
        ],

        'experience' =>
        [
            ['name' => 'List experience'],
            ['name' => 'Create experience'],
            ['name' => 'Delete experience'],
            ['name' => 'Update experience'],
        ],

        'myweek' =>
        [
            ['name' => 'List myweek'],
            ['name' => 'Create myweek'],
            ['name' => 'Delete myweek'],
            ['name' => 'Update myweek'],
        ],

        'purchase' =>
        [
            ['name' => 'List purchase'],
            ['name' => 'Create purchase'],
            ['name' => 'Delete purchase'],
            ['name' => 'Update purchase'],
        ]
        ,

        'wallet_interest' =>
        [
            ['name' => 'List wallet interest'],
            ['name' => 'Create wallet interest'],
        
        ]


       
      
    ];

    /**
     * @var
     */
    public $adminUser;

    /**
     * InstallAppHandler constructor.
     */
    public function __construct()
    {
        $this->roles = collect($this->roles);
        $this->permissions = collect($this->permissions);
    }

    /**
     * @param $installationData
     * @param \Closure $next
     * @return mixed
     */
    public function handle($installationData, Closure $next)
    {
        $this->createRoles()->createPermissions()->createAdminUser((array) $installationData)->assignAdminRoleToAdminUser()->assignAllPermissionsToAdminRole();
        event(new ApplicationWasInstalled($this->adminUser, $this->roles, $this->permissions));

        return $next($installationData);
    }

    /**
     * @return $this
     */
    public function createRoles()
    {
        $this->roles = $this->roles->map(function ($role) {
            return Role::create($role);
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function createPermissions()
    {
        $this->permissions = $this->permissions->map(function ($group) {
            return collect($group)->map(function ($permission) {
                return Permission::create($permission);
            });
        });

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     * @throws ValidationException
     */
    public function createAdminUser(array $attributes = [])
    {
        $attributes['name'] = "Admin";
        $attributes['email'] = "admin@fansinc.io";
        $attributes['password'] = "HtSlm@123";
        $attributes['password_confirmation'] = "HtSlm@123";
        $attributes['mobile']='9999999991';
        $validator = validator($attributes, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'mobile'=>'required|min:10',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $u = new User();
        $this->adminUser = $u->create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'mobile' => $attributes['mobile'],
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    public function assignAdminRoleToAdminUser()
    {
        $this->adminUser->assignRole('Administrator');

        return $this;
    }

    /**
     * @return $this
     */
    public function assignAllPermissionsToAdminRole()
    {
        $role = Role::where('name', 'Administrator')->firstOrFail();
        $this->permissions->flatten()->each(function ($permission) use ($role) {
            $role->givePermissionTo($permission);
        });

        return $this;
    }
}

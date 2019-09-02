<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;
use App\User;

class Role extends Model
{

    // const Role_Admin = 'Role_Admin';
    // const Role_Management = 'Role_Management';
    // const Role_Merchant = 'Role_Merchant';
    // const Role_Dispatcher = 'Role_Dispatcher';

    protected $fillable = ['name', 'display_name'];

    protected $cast = [
        'name' => 'string', 
        'display_name' => 'string',
        'id' => 'integer'
    ];

    public static $rules = [];

    protected static $roleheirachy = [
        self::Role_Admin => '[*]',
        self::Role_Management => [
            self::Role_Merchant => 'Role_Merchant',
            self::Role_Dispatcher => 'Role_Dispatcher'
        ],
    ];

    public static function getAllowedRoles(String $role)
    {
        if(isset(self::$roleheirachy[$role])) {
            return self::$roleheirachy[$role];
        }
        return [];
    }

    public static function getRoleList()
    {
        return [
            static::Role_Admin => 'Admin',
            static::Role_Management => 'Management',
            static::Role_Merchant => 'Merchant',
            static::Role_Dispatcher => 'Dispatcher'
        ];
    }


    public function users()
    {
        return $this->belongsTo('App\User');
    }
}

class RoleChecker
{
    /**
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function check(User $user, String $role)
    {
        //admin has everything
        if($user->hasRole(UserRole::Role_Admin)) {
            return true;
        } else if($user->hasRole(UserRole::Role_Management)) {
            $managementRoles = UserRole::getAllowedRoles(UserRole::Role_Management);
        }

        if(in_array($role, $managementRoles)) {
            return true;
        }

        return $user->hasRole($role);
    }

    public static function Admin() : Role
    {
        return Role::where('display_name', 'admin' )->first();

    }

    public static function Merchant() : Role
    {
        return Role::where('display_name', 'merchant')->first();
    } 

    public static function Dispatch() : Role
    {
        return Role::where('display_name', 'dispatch')->first();
    }
}
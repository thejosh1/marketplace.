<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword as IlluminateCanResetPassword;
use App\Role;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, IlluminateCanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'username', 'email', 'password',
    ];

    protected $isAdmin = bool;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => 'array'
    ];

    public function generateToken()
    {
        $this->api_token = str_random(60);
        $this->save();

        return $this->api_token;
    }

    public function orders()
    {
        return $this->hasMany('App\order');
    }

    public function address()
    {
        return $this->hasOne('App\Address');
    }

    public function review()
    {
        return $this->hasMany('App\ProductReview');
    }

    public function role()
    {
        return $this->hasOne('App\Role');
    }

    public function getRole(Role $role)
    {
        $role = $this->getRoles();
        $roles[] = $role;

        $roles = array_unique($roles);
        $this->setRoles($roles);

        return $this;
    }

    /**
     * @param array $roles
     * @return $this
     */

    public function setRoles(array $roles)
    {
        $this->setAttribute('roles', $roles);
        return $this;
    }

    /**
     * @param array| $role
     */
    public function hasRole($role)
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * @param array
     * @return mixed
     */

    public function hasRoles($roles)
    {
        $currentRoles = $this->getRoles();
        foreach ($roles as $role) {
            if (!in_array($role, $currentRoles)) {
                return false;
            }
            return true;
        }
    }

    /**
     * @return array
     */

    public function getRoles()
    {
        $roles = $this->getAttribute('roles');

        if (is_null($roles)) {
            $roles = [];
        }

        return $roles;
    }

    // public function getAdmin(Request $request)
    // {
    //     $admin_id = Auth::user()->id;
    //     $this->$admin_id = User::where('role_id', $admin_id);
    //     $admin = Auth::user()->find($admin_id);
    // }
}

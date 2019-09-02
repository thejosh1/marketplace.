<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Role\RoleChecker;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;


class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function __construct(RoleChecker $roleChecker)
    {
        $this->$roleChecker = $roleChecker;
    }

    
    public function handle($request, Closure $next, $role)
    {
        $user = Auth::guard()->user();
        if(! $this->roleChecker->check($role, $user)) {
            throw new AuthorizationException('You have no permission to check this page');
        }
        return $next($request);
    }
}

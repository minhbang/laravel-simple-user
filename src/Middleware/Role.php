<?php
namespace Minhbang\User\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * Class Role
 *
 * @package Minhbang\User\Middleware
 */
class Role
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $role
     * @param string $exact
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $role, $exact = '')
    {
        if ($user = user()) {
            if ($user->is($role, $exact === 'exact')) {
                return $next($request);
            } else {
                abort(403, trans('common.forbidden'));
            }
        } else {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('auth/login');
            }
        }
    }
}

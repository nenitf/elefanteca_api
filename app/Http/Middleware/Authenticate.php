<?php

namespace App\Http\Middleware;

use App\Repositories\Doctrine\UsuariosRepository;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            if(is_null(\Auth::user())) {
                throw new \Exception();
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json('Não autorizado', 401);
        }
    }
}

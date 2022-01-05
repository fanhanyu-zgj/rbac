<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClearanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::user()->hasPermissionTo('Administer roles & permissions')){
            return $next($request);
        }
        if($request->is('posts/create')){
            if(!Auth::user()->hasPermissionTo('Create Post')){
                abort('401');
            }else{
                return $next($request);
            }
        }
        if($request->is('posts/edit/create')){
            if(!Auth::user()->hasPermissionTo('Edit Post')){
                abort('401');
            }else{
                return $next($request);
            }
        }
        if($request->ismethod('Delete')){
            if(!Auth::user()->hasPermissionTo('Delete Post')){
                abort('401');
            }else{
                return $next($request);
            }
        }
        return $next($request);
    }
}

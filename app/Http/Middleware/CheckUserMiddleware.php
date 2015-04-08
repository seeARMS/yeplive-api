<?php namespace App\Http\Middleware;

use Closure;

class CheckUserMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$id = $request -> route('id');

		$user = \JWTAuth::parseToken()->toUser();
	
		if($id != $user->id)
		{
			return response()->json(['error' => 'unauthorized', 'messages' => ['token does not match user']], 401);
		}
	
		return $next($request);
	}

}

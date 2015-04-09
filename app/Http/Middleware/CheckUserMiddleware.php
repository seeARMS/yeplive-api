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
<<<<<<< HEAD
	
		if($id != (string) $user->user_id)
=======
		
		if($id != $user->user_id)
>>>>>>> 80be9cc94ddeaa33af880fa80ef913c03636c7eb
		{
			return response()->json(['error' => 'unauthorized', 'messages' => ['token does not match user']], 401);
		}
	
		return $next($request);
	}

}

<?php

namespace App\Http\Middleware;

use App\Helpers\AppGlobals;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

use function App\wt_parse_token;

class ApiAuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        // Get bearer token from request
        $token = wt_parse_token($request);

        // If not found then fail
        if ('' == $token) {
            return wt_api_json_error('Authentication failed (Token Empty)', 401, $request->header());
        }

        // Validate token

        if($request->user_id==''){
            return wt_api_json_error("Authenticated User ID is required");
        }
        $user = User::find($request->user_id);
        if(empty($user)){
            return wt_api_json_error("No user found");
        }


        // Check Rights - START --------------------------------------------------------------------------------------------


        // Check Rights - END ---------------------------------------------------------------------------------------------

        // Save authenticated user and other user selections in the app globals for user later
        AppGlobals::setUser($user);


        //return

        return $next($request);
    }
}

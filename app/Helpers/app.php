<?php

namespace App;

use App\Helpers\AppGlobals;
use Exception;
use Illuminate\Support\Facades\Auth;

function get_user_id($request)
{
    if($request->user_id==''){
        throw new Exception("Authenticated User Id is required");
    }
    
    return $request->user_id;
}

function get_user()
{
    $user = AppGlobals::getUser();
    return $user;
}

function get_user_role()
{
    $user = get_user();
    $role = $user->getRoleNames()->first();

    return $role;
}

function get_total_working_hour()
{
    return 8;
}

function get_total_working_days()
{
    return 30;
}

function get_company_id()
{
    return Auth::user()->company_id;
}

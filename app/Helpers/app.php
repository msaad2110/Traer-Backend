<?php

namespace App;

use Illuminate\Support\Facades\Auth;

function get_user_id(){
    return Auth::user()->id;
}

function get_user(){
    return Auth::user();
}

function get_user_role(){
    $user=get_user();
    $role=$user->getRoleNames()->first();

    return $role;
}

function get_total_working_hour(){
    return 8;
}

function get_total_working_days(){
    return 30;
}

function get_company_id(){
    return Auth::user()->company_id;
}

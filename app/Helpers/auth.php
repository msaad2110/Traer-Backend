<?php

namespace App;
use Illuminate\Http\Request;

function wt_parse_token(Request $request)
{
    // Get authorization header value
    $response = explode(' ', $request->header('Authorization'));

    // If 2nd part not found then token is not valid
    if (!isset($response[1])) {
        return '';
    }

    // Get bearer token from it
    return trim($response[1]);
}

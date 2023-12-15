<?php
function wt_api_json_response($success = true, $message = '', $data = '', $code = 200, $cookie = null, $logged_in_user_data = null)
{
    // Make the array
    $arr = [
        'success' => $success,
        'message' => $message,
        'data' => $data
    ];


    // Make the json response
    $response = response()->json($arr, $code);

    // If cookie is set the attach it
    if ($cookie) {
        $response->withCookie($cookie);
    }

    // Send response
    return $response;
}

/**
 * Return failure json for api calls.
 *
 * @param string $message
 * @param int $code
 * @param mixed $data
 *
 * @return JsonResponse
 */
function wt_api_json_error($message, $code = 500, $data = [])
{
    return wt_api_json_response(false, $message, $data, $code);
}

/**
 *  Return success json for api calls.
 *
 * @param $data
 * @param null $cookie
 * @param '' $message
 *
 * @return JsonResponse
 */
function wt_api_json_success($data, $cookie = null, $message = '')
{
    return wt_api_json_response(true, $message, $data, 200, $cookie);
}

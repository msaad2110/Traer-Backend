<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'phone' => 'required|unique:users,phone',
                    'country' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|confirmed',
                    'is_traveller' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                throw new Exception($validateUser->errors()->first());
            }

            $requestData = $request->all();
            $requestData['password'] = Hash::make($request->password);
            $user = User::create($requestData);

            $token = $user->createToken("API TOKEN")->plainTextToken;

            $jsonObject = (object)[
                'user' => $user,
                'token' => $token
            ];

            return wt_api_json_success($jsonObject, null, "User created successfully");
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ], [
            'email.exists' => 'The provided email does not match our records'
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->all());
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = User::where('email', $request->email)->whereNull('deleted_at')->first();
            $auth_token = $user->createToken('auth_token')->plainTextToken;

            $user_data = (object)[
                'token' => $auth_token,
                'user' => $user,
            ];

            return wt_api_json_success($user_data);
        } else {
            return wt_api_json_error("The password is not correct");
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Seshac\Otp\Otp;

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
            return wt_api_json_error($validate->errors()->first());
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

    public function forgot_password(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|exists:users,email'
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        $user = User::where('email', $request->email)->first();
        $otp = Otp::setValidity(740)->generate($user->email); // generating the otp based on the user email

        Mail::to($user->email)->send(new OTPMail($user, $otp));

        return wt_api_json_success(null, null, "OTP sent to the user");
    }

    public function verify_otp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'otp' => 'required'
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        try {
            $user = User::where('email', $request->email)->first();
            $otp = Otp::validate($user->email, $request->otp); // generating the otp based on the user email

            if ($otp->status) {
                return wt_api_json_success(null, null, "OTP verified");
            } else {
                throw new Exception($otp->message);
            }
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }

    public function reset_password(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed'
            ]
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        try {
            $user = User::where('email', $request->email)->first();
            $user->update([
                'password' => bcrypt($request->password)
            ]);

            return wt_api_json_success(null, null, "Password has been successfully reset");
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }
}

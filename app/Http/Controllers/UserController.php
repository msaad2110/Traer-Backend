<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::whereNull('deleted_at')->get();
        return wt_api_json_success($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return wt_api_json_success($user);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $action = $request->input('action');
        try {

            $user = User::findOrFail($id);
            if ($action == 'update_profile') {
                $validate = Validator::make($request->all(), [
                    'email' => 'required|unique:users,email,' . $id,
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'phone' => 'required|unique:users,phone,' . $id,
                ]);

                if ($validate->fails()) {
                    return wt_api_json_error($validate->errors()->first());
                }


                $requestData = $request->all();

                $user->update($requestData);

                return wt_api_json_success(null, null, "Profile Updated");
            } else if ($action == 'change_password') {

                $validate = Validator::make($request->all(), [
                    'password' => [
                        'required',
                        'string',
                        // Password::min(8)
                        //     ->mixedCase()
                        //     ->numbers()
                        //     ->symbols(),
                        'confirmed'
                    ],
                    'old_password' => [
                        'required', function ($attribute, $value, $fail) use($user) {
                            if (!Hash::check($value, $user->password)) {
                                $fail('Old Password didn\'t match');
                            }
                        },
                    ],
                ]);

                if ($validate->fails()) {
                    return wt_api_json_error($validate->errors()->first());
                }

                $user->update([
                    'password' => bcrypt($request->password)
                ]);

                return wt_api_json_success(null, null, "Password changed successfully");
            }
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update([
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by_id' => $request->user_id
            ]);

            return wt_api_json_success('User Deleted Successfully');
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }
}

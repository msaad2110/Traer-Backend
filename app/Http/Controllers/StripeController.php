<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function App\get_user_id;

class StripeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = get_user_id($request);
        $user = User::findOrFail($user_id);
        $customer = $user->createOrGetStripeCustomer();
        $intent = $user->createSetupIntent();

        return wt_api_json_success($intent);
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
        $request->validate(
            [
                'user_id' => 'required|exists:users,id',
                'payment_method' => 'required'
            ],
            [
                'payment_method.required' => 'A payment_method sent from stripe card confirmation API is required'
            ]
        );

        try {
            $user_id = get_user_id($request);
            $user = User::findOrFail($user_id);
            $user->updateDefaultPaymentMethod($request->payment_method);

            return wt_api_json_success(null, null, "Card Added Successfully");
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function payment_methods(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        $id = $request->input('user_id');
        $user = User::find($id);
        $paymentMethods = $user->paymentMethods();

        return wt_api_json_success($paymentMethods);
    }
}

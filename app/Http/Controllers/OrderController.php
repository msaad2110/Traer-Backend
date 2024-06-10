<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function App\get_user_id;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = get_user_id($request);
        $is_traveller = $request->input('is_traveller');
        $status = $request->input('status');
        $orders = Order::whereNull('deleted_at');

        if ($is_traveller) {
            $orders->where('created_by_id', $user_id);
        } else {
            $orders->where('customer_id', $user_id);
        }

        if ($status != '') {
            $orders->where('status', $status);
        }


        $orders = $orders->with('trip', 'created_by')->get();

        return wt_api_json_success($orders);
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
        $validate = Validator::make($request->all(), [
            'luggage_type_id' => 'required|exists:luggage_types,id',
            'trip_id' => 'required|exists:trips,id',
            'product_space' => 'required',
            'product_value' => 'required',
            'description' => 'required',
            'user_id' => 'required',
            'customer_email' => 'required|email|exists:users,email'
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        try {

            $requestData = $request->all();
            $requestData['created_by_id'] = get_user_id($request);
            $requestData['updated_by_id'] = get_user_id($request);

            $customer = User::where('email', $request->customer_email)->first();

            $requestData['customer_id'] = $customer->id;
            $requestData['tracking_number'] = Order::getNextCode();

            $Order = Order::create($requestData);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }

        return wt_api_json_success("Order Saved Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $trip = Order::findOrFail($id);
            return wt_api_json_success($trip);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // $validate = Validator::make($request->all(), [
        //     'luggage_type_id' => 'required|exists:luggage_types,id',
        //     'trip_id' => 'required|exists:trips,id',
        //     'product_space' => 'required',
        //     'product_value' => 'required',
        //     'description' => 'required',
        //     'user_id' => 'required',
        // ]);

        // if ($validate->fails()) {
        //     return wt_api_json_error($validate->errors()->first());
        // }

        try {

            $requestData = $request->all();
            $requestData['updated_by_id'] = get_user_id($request);

            $Order = Order::find($id);
            $Order->update($requestData);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }

        return wt_api_json_success("Order Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        try {

            $Order = Order::findOrFail($id);
            $Order->update([
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by_id' => get_user_id($request)
            ]);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }

        return wt_api_json_success("Order Successfully Deleted");
    }

    public function track(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'tracking_number' => 'required|exists:orders,tracking_number',
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        $tracking_number = $request->input('tracking_number');

        $order = Order::where('tracking_number', $tracking_number)->first();

        return wt_api_json_success($order);
    }

    public function pay(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'payment_method_id' => 'required',
            'user_id' => 'required',
            'order_id' => 'required'
        ], [
            'payment_method_id.required' => 'Please select a payment method',
            'order_id.required' => 'Please select an order to pay'
        ]);
        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        try {

            $order = Order::find($request->order_id);
            $customer = User::find($order->customer_id);
            if (empty($customer)) {
                return wt_api_json_error("No customer found on this order");
            }
            $stripeCharge = $customer->charge(
                $order->product_value,
                $request->payment_method_id
            );

            // if ($stripeCharge) {
            Transaction::create([
                'amount' => $order->product_value,
                'user_id' => $order->created_by_id
            ]);
            // }
            return wt_api_json_success($stripeCharge);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }
}

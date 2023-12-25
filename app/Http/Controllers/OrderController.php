<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
        $trips = Order::whereNull('deleted_at')->where('created_by_id', $user_id)->get();

        return wt_api_json_success($trips);
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
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        try {

            $requestData = $request->all();
            $requestData['created_by_id'] = get_user_id($request);
            $requestData['updated_by_id'] = get_user_id($request);

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

        $validate = Validator::make($request->all(), [
            'luggage_type_id' => 'required|exists:luggage_types,id',
            'trip_id' => 'required|exists:trips,id',
            'product_space' => 'required',
            'product_value' => 'required',
            'description' => 'required',
            'user_id' => 'required',
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

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
}

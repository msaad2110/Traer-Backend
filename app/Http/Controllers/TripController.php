<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function App\get_user_id;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = get_user_id($request);
        $sort_order = $request->input('sort_order');
        $sort_by = $request->input('sort_by');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $travelling_from = $request->input('travelling_from');
        $luggage_space = $request->input('luggage_space');
        $commission_start = $request->input('commission_start');
        $commission_end = $request->input('commission_end');
        $is_traveller = $request->input('is_traveller');

        $user = User::find($user_id);

        $trips = Trip::whereNull('deleted_at');

        if ($start_date != '' && $end_date != '') {
            $trips->where([
                ['start_date', '<=', $start_date],
                ['end_date', '<=', $end_date],
            ]);
        }

        if ($is_traveller == 1) {
            $trips->where('created_by_id', $user_id);
        }

        if ($travelling_from != '') {
            $trips->where('travelling_from', 'LIKE', "%{$travelling_from}%");
        }
        if ($luggage_space != '') {
            $trips->where('luggage_space', 'LIKE', "%{$luggage_space}%");
        }

        if ($commission_start != '' && $commission_end != '') {
            $trips->whereBetween('commission', [$commission_start, $commission_end]);
        }

        if ($sort_by != '' && $sort_order != '') {
            $trips = $trips->orderBy($sort_by, $sort_order);
        }

        $trips = $trips->with('luggage_type', function ($query) {
            $query->select('id', 'name');
        })
            ->with('created_by')
            ->get();

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
            'travelling_from' => 'required',
            'travelling_to' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'luggage_space' => 'required',
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        try {

            $requestData = $request->all();
            $requestData['created_by_id'] = get_user_id($request);
            $requestData['updated_by_id'] = get_user_id($request);

            $luggageType = Trip::create($requestData);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }

        return wt_api_json_success("Trip Saved Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $trip = Trip::findOrFail($id);
            return wt_api_json_success($trip);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function edit(Trip $trip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validate = Validator::make($request->all(), [
            'luggage_type_id' => 'required|exists:luggage_types,id',
            'travelling_from' => 'required',
            'travelling_to' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'luggage_space' => 'required',
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        try {

            $requestData = $request->all();
            $requestData['updated_by_id'] = get_user_id($request);

            $luggageType = Trip::find($id);
            $luggageType->update($requestData);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }

        return wt_api_json_success("Trip Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        try {

            $luggageType = Trip::findOrFail($id);
            $luggageType->update([
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by_id' => get_user_id($request)
            ]);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }

        return wt_api_json_success("Trip Successfully Deleted");
    }
}

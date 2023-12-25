<?php

namespace App\Http\Controllers;

use App\Models\LuggageType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function App\get_user_id;

class LuggageTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $luggageTypes = LuggageType::whereNull('deleted_at')->get();
        return wt_api_json_success($luggageTypes);
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
            'name' => 'required',
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        try {
            $luggageType = LuggageType::create([
                'name' => $request->name
            ]);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }

        return wt_api_json_success("Luggage Type Successfully Created");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LuggageType  $luggageType
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $luggageType = LuggageType::findOrFail($id);
            return wt_api_json_success($luggageType);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LuggageType  $luggageType
     * @return \Illuminate\Http\Response
     */
    public function edit(LuggageType $luggageType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LuggageType  $luggageType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        try {

            $luggageType = LuggageType::findOrFail($id);
            $luggageType->update([
                'name' => $request->name
            ]);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }

        return wt_api_json_success("Luggage Type Successfully Updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LuggageType  $luggageType
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {

        try {

            $luggageType = LuggageType::findOrFail($id);
            $luggageType->update([
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by_id' => get_user_id($request)
            ]);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }

        return wt_api_json_success("Luggage Type Successfully Deleted");
    }
}

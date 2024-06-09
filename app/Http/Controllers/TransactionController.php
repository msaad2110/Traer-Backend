<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function balance(Request $request)
    {
        $user_id = $request->input('user_id');
        $balance = Transaction::where('user_id', $user_id)->sum('amount');
        return wt_api_json_success($balance);
    }
}

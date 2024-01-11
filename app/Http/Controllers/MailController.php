<?php

namespace App\Http\Controllers;

use App\Mail\WebsiteMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MailController extends Controller
{
    public function website_mail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to' => 'required|email',
            'subject' => 'required|max:200',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return wt_api_json_error($validator->errors()->first());
        }
        $to = $request->input('to');
        $subject = $request->input('subject');
        $content = $request->input('content');
        Mail::to($to)->send(new WebsiteMail($subject, $content));

        return wt_api_json_success("Mail sent successfully");
    }
}

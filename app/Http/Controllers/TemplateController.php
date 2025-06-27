<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\SMSTemplate;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public static function index(){
        return view('admin.template.index',[
            'smsTemplates' => SMSTemplate::all(),
            'emailTemplates' => EmailTemplate::all(),
        ]);
    }

    public static function update(Request $request,$id){
        $sms = SMSTemplate::find($id);
        $sms->body = $request->body;
        $sms->save();
        return back()->with('success','SMS Template Updated');
    }

    public static function emailTemplate(Request $request,$id){
        $Email = EmailTemplate::find($id);
        $Email->body = $request->body;
        $Email->save();
        return back()->with('success','Email Template Updated');
    }
}

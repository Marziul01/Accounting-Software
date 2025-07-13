<?php

namespace App\Http\Controllers;

use App\Models\Home;
use Illuminate\Http\Request;

class Homecontroller extends Controller
{
    public function index()
    {
        return view('frontend.home.home',[
            'home' => Home::find(1),
        ]);
    }

}

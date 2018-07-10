<?php

namespace App\Http\Controllers;

use App\Models\FieldsValue;
use App\Models\Product;
use App\Models\ProductField;
use Illuminate\Http\Request;
use App\Models\Users\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
}

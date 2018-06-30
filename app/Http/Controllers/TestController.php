<?php

namespace App\Http\Controllers;

use App\Jobs\ParseCategories;
use Illuminate\Http\Request;

class TestController extends Controller
{
    function testQueue(){
        ParseCategories::dispatchNow();
    }
}

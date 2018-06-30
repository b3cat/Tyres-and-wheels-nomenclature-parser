<?php

namespace App\Http\Controllers;

use App\Jobs\ParseManufactures;
use Illuminate\Http\Request;

class TestController extends Controller
{
    function testQueue(){
        ParseManufactures::dispatchNow();
    }
}

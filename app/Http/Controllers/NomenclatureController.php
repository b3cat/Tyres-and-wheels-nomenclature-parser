<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Whitelist;
use Illuminate\Http\Request;

class NomenclatureController extends Controller
{
    function index(Category $categoryModel){
        $category = $categoryModel->find(1);
        dd($category->whitelist);
        return view('nomenclature.index');
    }
}

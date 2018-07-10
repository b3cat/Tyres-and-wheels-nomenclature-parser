<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Field;
use App\Models\FieldsValue;
use App\Models\Nomenclature;
use App\Models\Product;
use App\Models\Whitelist;
use Illuminate\Http\Request;

class NomenclatureController extends Controller
{
    function index()
    {
        return view('nomenclature.index');
    }

    function testParser()
    {
        return view('nomenclature.test');
    }
    function errors(){
//        dd(Field::all()->where('group', 1));
        $errorProducts = Product::errors()->paginate(10);
        $fieldsValuesLists = Field::allValueLists();
        return view('nomenclature.errors', [
            'errorProducts' => $errorProducts,
            'fieldsValuesLists' => $fieldsValuesLists
        ]);
    }
    function whitelists(Category $categoryModel){
        $manufacturers = $categoryModel
            ->where('category_parent_id', 1)
            ->orWhere('category_parent_id', 2)
            ->get();
        return view('nomenclature.whitelists.index',[
            'manufacturers' => $manufacturers,
            'models' => []
        ]);
    }
    function whitelistsSearch(Request $request, Category $categoryModel){
        if($request->ajax()){
            $searchRequest = $request->{'search'};
            $manufacturers = [];
            $models = [];
            if(strlen($searchRequest) > 0){
                $results = $categoryModel::search($searchRequest)->get();
            }else{
                $results = $categoryModel
                    ->where('category_parent_id', 1)
                    ->orWhere('category_parent_id', 2)
                    ->get();
            }
            foreach ($results as $result){
                if( ($result->{'category_parent_id'} === 1) or ($result->{'parent_category_id'} === 2)){
                    array_push($manufacturers, $result);
                } else {
                    array_push($models, $result);
                }
            }
            return view('nomenclature.whitelists._modules._categories', [
                'manufacturers' => $manufacturers,
                'models' => $models
            ]);
        }
    }
    function whitelistsGetModels($id, Category $categoryModel){
        $models = $categoryModel->where('category_parent_id', $id)->get();
        return view('nomenclature.whitelists._modules._models', [
            'models' => $models
        ]);
    }
    function whitelistsGet($id, Category $categoryModel){
        $category = $categoryModel->where('category_id', $id)->first();
        $currentWhitelist = $category->whitelist;
        $lineBreaker = '&#013;&#010;';
        $whitelist = '';
        foreach ($currentWhitelist as $item){
            $whitelist .= $item->{'string'}.$lineBreaker;
        }
        return $whitelist;
    }
}

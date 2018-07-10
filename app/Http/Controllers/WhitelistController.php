<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Whitelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpParser\Node\Stmt\Return_;

class WhitelistController extends Controller
{
    function index(){

    }


    function autoCompleteSelect(Request $request, Category $categoriesModel){
        $results = array();
        if($request->ajax()){

            $term = trim($request->{'search'});
            if (empty($term)) {
                return \Response::json([]);
            }
            $categories = $categoriesModel::search($term)->where('category_parent_id', 1)->take(5)->get();
            foreach ($categories as $category){
                $results[] = [
                  'id' => $category->{'category_id'},
                  'text' => $category->{'name_ru-RU'}
                ];
            }
        }
        return \Response::json($results);
    }
    /**
     * @param int $id
     * @param Category $categoryModel
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function makeWhitelist(int $id, Category $categoryModel){
        $category = $categoryModel->find($id);
        $currentWhitelist = $category->whitelist;
        $lineBreaker = '&#013;&#010;';
        $whitelist = '';
        foreach ($currentWhitelist as $item){
            $whitelist .= $item->{'string'}.$lineBreaker;
        }
        return view('whitelist.make', [
            'category' => $category,
            'whitelist' => $whitelist
        ]);
    }
    function saveWhitelist(Request $request, Whitelist $whitelistModel){
        $data  = $request->all(['whitelisted_id', 'whitelist']);
        $whitelist = preg_split('/\r\n|[\r\n]/', $data['whitelist']);
        $whitelistedType = 'App\Models\Category';
        $whitelistedId = $data['whitelisted_id'];
        foreach ($whitelist as $string){
            $whitelistModel::firstOrCreate([
                'whitelisted_type' => $whitelistedType,
                'whitelisted_id' => $whitelistedId,
                'string' => $string
            ]);
        }
        return redirect()->back();
    }
}

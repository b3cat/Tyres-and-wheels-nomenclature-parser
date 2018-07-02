<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Whitelist;
use Illuminate\Http\Request;

class WhitelistController extends Controller
{
    function index(){

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

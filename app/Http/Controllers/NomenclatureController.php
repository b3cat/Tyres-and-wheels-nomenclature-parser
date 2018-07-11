<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ErrorSourceString;
use App\Models\Field;
use App\Models\FieldsValue;
use App\Models\Nomenclature;
use App\Models\Parser\Parser;
use App\Models\Product;
use App\Models\ProductField;
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

    function errors()
    {
//        dd(Field::all()->where('group', 1));
        $errorProducts = Product::errors()->paginate(10);
        $fieldsValuesLists = Field::allValueLists();
        return view('nomenclature.errors', [
            'errorProducts' => $errorProducts,
            'fieldsValuesLists' => $fieldsValuesLists
        ]);
    }

    function whitelists(Category $categoryModel)
    {
        $manufacturers = $categoryModel
            ->where('category_parent_id', 1)
            ->orWhere('category_parent_id', 2)
            ->with('parentCategory')
            ->get();
        return view('nomenclature.whitelists.index', [
            'manufacturers' => $manufacturers,
            'models' => []
        ]);
    }

    function whitelistsSearch(Request $request, Category $categoryModel)
    {
        if ($request->ajax()) {
            $searchRequest = $request->{'search'};
            $manufacturers = [];
            $models = [];
            if (strlen($searchRequest) > 0) {
                $results = $categoryModel::search($searchRequest)->get();
            } else {
                $results = $categoryModel
                    ->where('category_parent_id', 1)
                    ->orWhere('category_parent_id', 2)
                    ->with('parentCategory')
                    ->get();
            }
            foreach ($results as $result) {
                if (($result->{'category_parent_id'} === 1) or ($result->{'parent_category_id'} === 2)) {
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

    function whitelistsGetModels($id, Category $categoryModel)
    {
        $models = $categoryModel->where('category_parent_id', $id)->get();
        $parentCategory = $categoryModel->where('category_id', $id)->first();
        return view('nomenclature.whitelists._modules._models', [
            'models' => $models,
            'parentCategory' => $parentCategory
        ]);
    }

    function whitelistsGet($id, Category $categoryModel)
    {
        $category = $categoryModel->where('category_id', $id)->first();
        $currentWhitelist = $category->whitelist;
        $lineBreaker = '&#013;&#010;';
        $whitelist = '';
        foreach ($currentWhitelist as $item) {
            $whitelist .= $item->{'string'} . $lineBreaker;
        }
        return json_encode([
            'whitelist' => $whitelist,
            'categoryId' => $category->{'id'}
        ]);
    }
    function saveWhitelist(Request $request, Whitelist $whitelistModel){
        if($request->ajax()){
            $data  = $request->all(['whitelisted_id', 'whitelist']);
            $whitelistModel::where('whitelisted_id', $data['whitelisted_id'])->delete();
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
        } else {
            abort(403);
        }
    }
    function parserError($id, Request $request, Product $productModel, ErrorSourceString $errorSourceString)
    {
        if ($request->ajax()) {
            $product = $productModel->find($id);
            $errorString = $errorSourceString::firstOrCreate([
                'source_string' => $product->{'source_string'},
                'product_id' => $product->{'id'}

            ]);
        } else {
            return 'error';
        }
    }

    /**
     * @param $id
     * @param Parser $parser
     * @param Product $productModel
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function parseAgain($id, Parser $parser, Product $productModel){
        $product = $productModel->find($id);
        $product->{'errors'} = false;
        $result = $parser->parse($product->{'source_string'});
        $fields = $result['fields'];
        $modelFields = $product->fields;
        $product->{'category_id'} = $result['model']['id'];
        foreach ($fields as $field){
            /**
             * @var ProductField $changingField
             */
            $changingField = $modelFields->where('field_id', $field['fieldId'])->first();
            $changingField->update([
                'fields_value_id' => $field['fieldsValueId'],
            ]);
            if (($field['fieldsValueId'] === 0) and ($field['fieldId'] !== 9)) {
                $product->{'errors'} = true;
            }
        }
        $product->save();
        $fieldsValuesLists = Field::allValueLists();
        return view('nomenclature.modules._productEdit',[
            'product' => $product,
            'fieldsValuesLists' => $fieldsValuesLists
        ]);
    }
}

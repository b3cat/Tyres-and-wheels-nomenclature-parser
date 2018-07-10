<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function updateFromErrors(Request $request, Product $productModel){
        $data = $request->all();
        $productId = $data['product_id'];
        $product = $productModel->find($productId);
        $fields = $product->fields;
        foreach ($fields as $field){
            $field->{'fields_value_id'} = $data[$field->{'field_id'}];
            $field->save();
        }
        $fieldsValuesLists = Field::allValueLists();
        return view('nomenclature.modules._productEdit', [
            'product' => $product,
            'fieldsValuesLists' => $fieldsValuesLists
        ]);
    }
}

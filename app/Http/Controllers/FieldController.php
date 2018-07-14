<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    function pairFields(Category $categoryModel){
        $mainCategories = $categoryModel->mainCategories();
        return view('nomenclature.parser.fields.pairFields', [
           'mainCategories' => $mainCategories
        ]);
    }
    function makePairFields(Request $request, Field $fieldModel, Category $categoryModel){
        $data = $request->all();
        $firstFieldId = $data['first'];
        $secondFieldId = $data['second'];
        $firstField = $fieldModel->where('field_id', $firstFieldId)->first();
        $firstField->{'pair_field_id'} = $secondFieldId;
        $secondField = $fieldModel->where('field_id', $secondFieldId)->first();
        $secondField->{'pair_field_id'} = $firstFieldId;
        $firstField->save();
        $secondField->save();
        $mainCategories = $categoryModel->mainCategories();

        return view('nomenclature.parser.fields.modules._showTable', [
            'mainCategories' => $mainCategories
        ]);
    }
}

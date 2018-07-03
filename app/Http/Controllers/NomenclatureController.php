<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Field;
use App\Models\FieldsValue;
use App\Models\Nomenclature;
use App\Models\Whitelist;
use Illuminate\Http\Request;

class NomenclatureController extends Controller
{
    function index(Category $categoryModel, Nomenclature $nomenclatureModel){
//        $sourceStrings = $nomenclatureModel->inRandomOrder()->get([
//            'sourceString',
//            'product_manufacturer'
//        ]);
//        $categories = $categoryModel->where('category_parent_id', '=', '1')->get();
//        $counter = 0;
//        $count = 2705;
//        foreach ($sourceStrings as $sourceString){
//            if(!$sourceString->isTire()){
//                $count--;
//                continue;
//            }
//            $str = $sourceString->{'sourceString'};
//            $categoryId = 0;
//            $modelId = 0;
//            foreach ($categories as $manufacturer){
//                $whitelist = '';
//                foreach ($manufacturer->whitelist as $value){
//                    $whitelist .= '|'.preg_quote($value->{'string'},'/');
//                }
//                $re = '/'.$manufacturer->{'name_ru-RU'}.$whitelist.'/mi';
////                dd($re);
//                preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE, 0);
//                if($matches){
//                    $categoryId = $manufacturer->{'category_id'};
//                }
//            }
//            if($categoryId){
//                $models = $categoryModel->where('category_parent_id', '=', $categoryId)->get();
//                foreach ($models as $model){
//                    $whitelist = preg_quote($model->{'name_ru-RU'},'/');
//                    foreach ($model->whitelist as $value){
//                        $whitelist .= '|'.preg_quote($value->{'string'},'/');
//                    }
//                    $re = '/'.$whitelist.'/mi';
//
//                    preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE, 0);
//                    if($matches){
//                        $modelId = $model->{'category_id'};
//                    }
//                }
//            }
//            if($modelId && $categoryId){
////                echo '<hr>'.$str.'<hr>';
//                $counter++;
//            } else {
//                echo 'model_id: '.($modelId ? $modelId : '');
//                echo 'category_id: '.($categoryId ? $categoryId : '');
//                echo '<hr><b>Не запарсил </b>'.$str.'<hr>';
//                echo '<br><br>';
//
//            }
//
//
//        }
//        echo $counter.' из '.$count;
    }
    function testParser(){
        return view('nomenclature.test');
    }
    function parse(Request $request){
        $data = $request->only('source_string');
        $sourceString = $data['source_string'];
        $response['sourceString'] = $sourceString;
        $response['manufacturer'] = $this->parseCategory($sourceString);
        $response['model'] = $this->parseCategory($sourceString, $response['manufacturer']['id']);
        $widthAndHeight = $this->parseWidthAndHeight($sourceString, 1, 2);
        $response['fields']['width'] = $widthAndHeight['width'];
        $response['fields']['height'] = $widthAndHeight['height'];
        $response['fields']['radius'] = $this->parseRadius($sourceString, 3);
        $response['fields']['carryingCapacityIndex'] = $this->parseCarryingCapacityIndex($sourceString, 4);
        $response['fields']['speedIndex'] = $this->parseSpeedIndex($sourceString, 5);
        $response['status'] = !preg_match('~\w~', $sourceString) ? 'OK' : 'Failed';
        return view('nomenclature.test', [
           'response' => $response
        ]);
    }

    /**
     * @param string $sourceString
     * @param int $parent_category
     * @return array
     */
    protected function parseCategory(string &$sourceString, $parent_category = 0){
        $response = [
            'id' => 0,
            'displayName' => ''
        ];
        $categoryModel = new Category;
        $categories = $categoryModel->all();
        $matches = array();
        foreach ($categories as $category){
            if(preg_match($category->whitelistRegExp(), $sourceString, $m)){

                if($category->isTire() && !$parent_category){
                    $response['id'] = $category->{'category_id'};
                    $response['displayName'] = $category->{'name_ru-RU'};
                    $matches = $m;
                }
                if($category->isModel() && $category->{'category_parent_id'} === $parent_category){
                    if(strlen($response['displayName']) < strlen($category->{'name_ru-RU'})){
                        $response['id'] = $category->{'category_id'};
                        $response['displayName'] = $category->{'name_ru-RU'};
                        $matches = $m;
                    }
                }
            }
        }
        $sourceString = str_replace($matches['category'], '',$sourceString, $c);
        return $response;
    }

    /**
     * @param string $sourceString
     * @param int $widthFieldId
     * @param int $heightFieldId
     * @return array
     */
    protected function parseWidthAndHeight(&$sourceString, $widthFieldId, $heightFieldId){
        $fieldModel = new Field;
        $fieldsValueModel = new FieldsValue;
        $response = [
            'width' => [
                'fieldId' => $widthFieldId,
                'fieldsValueId' => 0,
                'displayValue' => '',
                'fieldName' => ''
            ],
            'height' => [
                'fieldId' => $heightFieldId,
                'fieldsValueId' => 0,
                'displayValue' => '',
                'fieldName' => ''
            ]
        ];
        $widthField  = $fieldModel->where('field_id', '=', $widthFieldId)->first();
        $response['width']['fieldName'] = $widthField->{'name_ru-RU'};
        $response['width']['fieldName'] = $widthField->{'name_ru-RU'};
        $heightField  = $fieldModel->where('field_id', '=', $heightFieldId)->first();
        $response['height']['fieldName'] = $heightField->{'name_ru-RU'};
        $regExp = '~'.$widthField->prepareForRegExp().'[/x\*]'.$heightField->prepareForRegExp().'~';
        if(preg_match($regExp, $sourceString, $m)){
            $response['width']['displayValue'] = $width = $m['field'.$widthFieldId];
            $response['width']['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $widthFieldId],
                ['name_ru-RU', '=', $response['width']['displayValue']]
            ])->first()->{'fields_value_id'};


            $response['height']['displayValue'] = $width = $m['field'.$heightFieldId];
            $response['height']['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $heightFieldId],
                ['name_ru-RU', '=', $response['height']['displayValue']]
            ])->first()->{'fields_value_id'};


            $sourceString = str_replace($m[0], '', $sourceString);
        }
        return $response;
    }

    /**
     * @param $sourceString
     * @param $radiusFieldId
     * @return array
     */
    protected function parseRadius(&$sourceString, $radiusFieldId){
        $fieldModel = new Field;
        $fieldsValueModel = new FieldsValue;
        $response = [
            'fieldId' => $radiusFieldId,
            'fieldsValueId' => 0,
            'displayValue' => '',
            'fieldName' => ''
        ];
        $radiusField = $fieldModel->where('field_id', '=', $radiusFieldId)->first();
        $response['fieldName'] = $radiusField->{'name_ru-RU'};
        $regExp = '~R'.$radiusField->prepareForRegExp().'~';
        if(preg_match($regExp, $sourceString, $m)){
            $response['displayValue'] = $m['field'.$radiusFieldId];
            $response['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $radiusFieldId],
                ['name_ru-RU', '=', $response['displayValue']]
            ])->first()->{'fields_value_id'};

            $sourceString = str_replace($m[0], '', $sourceString);
        }
        return $response;
    }

    /**
     * @param $sourceString
     * @param $carryingCapacityIndexFieldId
     * @return array
     */
    protected function parseCarryingCapacityIndex(&$sourceString, $carryingCapacityIndexFieldId){
        $fieldModel = new Field;
        $fieldsValueModel = new FieldsValue;
        $response = [
            'fieldId' => $carryingCapacityIndexFieldId,
            'fieldsValueId' => 0,
            'displayValue' => '',
            'fieldName' => ''
        ];
        $carryingCapacityIndexField = $fieldModel->where('field_id', '=', $carryingCapacityIndexFieldId)->first();
        $response['fieldName'] = $carryingCapacityIndexField->{'name_ru-RU'};
        $regExp = '~'.$carryingCapacityIndexField->prepareForRegExp().'~';
        if(preg_match($regExp, $sourceString, $m)){
            $response['displayValue'] = $m['field'.$carryingCapacityIndexFieldId];
            $response['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $carryingCapacityIndexFieldId],
                ['name_ru-RU', '=', $response['displayValue']]
            ])->first()->{'fields_value_id'};

            $sourceString = str_replace($m[0], '', $sourceString);
        }
        return $response;
    }
    protected function parseSpeedIndex(&$sourceString, $speedIndexFieldId){
        $fieldModel = new Field;
        $fieldsValueModel = new FieldsValue;
        $response = [
            'fieldId' => $speedIndexFieldId,
            'fieldsValueId' => 0,
            'displayValue' => '',
            'fieldName' => ''
        ];
        $speedIndexField = $fieldModel->where('field_id', '=', $speedIndexFieldId)->first();
        $response['fieldName'] = $speedIndexField->{'name_ru-RU'};
        $regExp = '~'.$speedIndexField->prepareForRegExp().'~i';
        if(preg_match($regExp, $sourceString, $m)){
            $response['displayValue'] = $m['field'.$speedIndexFieldId];
            $response['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $speedIndexFieldId],
                ['name_ru-RU', '=', $response['displayValue']]
            ])->first()->{'fields_value_id'};
            $sourceString = str_replace($m[0], '', $sourceString);
        }
        return $response;
    }
}

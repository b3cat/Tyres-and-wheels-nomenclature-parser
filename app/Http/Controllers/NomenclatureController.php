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
    function index()
    {
        return view('nomenclature.index');
    }

    function testParser()
    {
        return view('nomenclature.test');
    }

    function parse(Request $request)
    {
        $gropId = 2; //что парсим (шины или диски)
        $data = $request->only('source_string');
        $sourceString = $data['source_string'];
        $sourceString = preg_replace('~,~', '.', $sourceString);
        $response['sourceString'] = $sourceString;
        $fieldModel = new Field;
        $fields = $fieldModel->where('group', $gropId)->get();
        if($gropId === 1){
            $parseSetting = [
                [
                    'regExpMask' => '~_[/x\*]~i',
                    'doNotTouchSourceString' => true
                ],
                ['regExpMask' => '~ [0-9]+[/x\*]_~i'], ['regExpMask' => '~R {0,1}_~'], ['regExpMask' => '~\s(?<toDelete>_)\w$~'], ['regExpMask' => '~\s_(?:\s|$)~i'],
                ['regExpMask' => '~_~'], ['regExpMask' => '~_~'], ['regExpMask' => '~_~'], ['regExpMask' => '~_~'], ['regExpMask' => '~_~']
            ];
        } else {
            $parseSetting = [
                [
                    'regExpMask' => '~_[\.[0-9]*[хx\*/]~i',
                    'doNotTouchSourceString' => true
                ],
                [
                    'regExpMask' => '~(?<toDelete>[0-9]+[\.]*[0-9]*[хx\*/]_)~ui',
                ],
                [
                    'regExpMask' => '~_[\.[0-9]*[хx\*/]~i',
                    'doNotTouchSourceString' => true
                ],
                [
                    'regExpMask' => '~(?<toDelete>[0-9]+[хx\*/]_)~ui',
                ],
                [
                    'regExpMask' => '~(?:DIA|d)\s*_~',
                ],
                [
                    'regExpMask' => '~ET\s*_~i',
                ],
                [
                    'regExpMask' => '~pizda~i',
                ],
                [
                    'regExpMask' => '~pizda~i',
                ],
            ];
        }

        foreach ($fields as $currentFieldId => $field){
            $response['fields'][$field->{'name_ru-RU'}] = $this->parseField($sourceString,
                $field->{'field_id'},
                $parseSetting[$currentFieldId]['regExpMask'], isset($parseSetting[$currentFieldId]['doNotTouchSourceString']) ? false : true);
        }
        $response['manufacturer'] = $this->parseCategory($sourceString);
        $response['model'] = $this->parseCategory($sourceString, $response['manufacturer']['id']);
        $response['status'] = !preg_match('~\w~', $sourceString) ? true : false;
        return view('nomenclature.test', [
            'response' => $response
        ]);
    }

//    protected function parse($sourceString, $groupId)
//    {
//        $sourceString = preg_replace('~,~', '.', $sourceString);
//        $response['sourceString'] = $sourceString;
//        $fieldModel = new Field;
//        $fields = $fieldModel->where('group', $groupId)->get();
//        $parseSetting = [
//            [
//                'regExpMask' => '~_[/x\*]~i',
//                'doNotTouchSourceString' => true
//            ],
//            ['regExpMask' => '~ [0-9]+[/x\*]_~i'], ['regExpMask' => '~R {0,1}_~'], ['regExpMask' => '~_~'], ['regExpMask' => '~\s_(?:\s|$)~i'],
//            ['regExpMask' => '~_~'], ['regExpMask' => '~_~'], ['regExpMask' => '~_~'], ['regExpMask' => '~_~'], ['regExpMask' => '~_~']
//        ];
//        foreach ($fields as $currentFieldId => $field){
//            $response['fields'][$field->{'name_ru-RU'}] = $this->parseField($sourceString,
//                $field->{'field_id'},
//                $parseSetting[$currentFieldId]['regExpMask'], isset($parseSetting[$currentFieldId]['doNotTouchSourceString']) ? false : true);
//        }
//        $response['manufacturer'] = $this->parseCategory($sourceString);
//        $response['model'] = $this->parseCategory($sourceString, $response['manufacturer']['id']);
//        $response['status'] = !preg_match('~\w~', $sourceString) ? true : false;
//        return $response;
//    }

    /**
     * @param string $sourceString
     * @param int $parent_category
     * @return mixed
     */
    protected function parseCategory(string &$sourceString, $parent_category = 0)
    {
        $response = [
            'id' => 0,
            'displayName' => '',
            'status' => false
        ];
        $matches[0] = '';
        $categoryModel = new Category;
        $categories = $categoryModel::search(strtolower($sourceString))->where('category_parent_id', $parent_category ? $parent_category : 2)->get();
        foreach ($categories as $category) {

            if (preg_match('~' . $category{'name_ru-RU'} . '~i', $sourceString, $m)) {
                if ((!$parent_category and ($category->{'category_parent_id'} === 1 or $category->{'category_parent_id'} === 2)) or $category->{'category_parent_id'} === $parent_category) {
                    $response['id'] = $category->{'category_id'};
                    $response['displayName'] = $category->{'name_ru-RU'};
                    $response['status'] = true;
                    $matches = $m;
                }
            }
        }
        $whitelist = Whitelist::search($sourceString)->get();
        foreach ($whitelist as $item) {
            if (preg_match('~' . $item{'string'} . '~i', $sourceString, $m)) {
                $result = $categoryModel->find($item->{'whitelisted_id'});
                if ((!$parent_category and ($result->{'category_parent_id'} === 1 or $result->{'category_parent_id'} === 2)) or $result->{'category_parent_id'} === $parent_category) {
                    dump($result);
                    $response['id'] = $result->{'category_id'};
                    $response['displayName'] = $result->{'name_ru-RU'};
                    $response['status'] = true;

                    $matches = $m;
                }
            }
        }
        $sourceString = str_replace($matches[0], '', $sourceString);

        if(!$response['id'])return false;
        return $response;
    }

    protected function parseField(&$sourceString, $fieldId, $regExpMask, $modifySourceString = true){
        $fieldModel = new Field;
        $fieldsValueModel = new FieldsValue;
        $response = [
            'fieldId' => $fieldId,
            'fieldsValueId' => 0,
            'displayValue' => '',
            'fieldName' => '',
            'status' => false,
        ];
        $field = $fieldModel->where('field_id', $fieldId)->first();
        $response['fieldName'] = $field->{'name_ru-RU'};
        $regExp = str_replace('_', $field->prepareForRegExp(), $regExpMask);
        dump('Сурс стринг: '.$sourceString);
        dump('Регулярка: '. $regExp);

        if(preg_match($regExp, $sourceString, $m)){
            dump($m);
            $response['displayValue'] = $m['field' . $fieldId];
            $response['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $fieldId],
                ['name_ru-RU', '=', $response['displayValue']]
            ])->first()->{'fields_value_id'};
            $response['status'] = true;
            if($modifySourceString){
                $sourceString = str_replace(isset($m['toDelete']) ? $m['toDelete'] : $m[0], '', $sourceString);
            }
        }
        return $response;
    }
}

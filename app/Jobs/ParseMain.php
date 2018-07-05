<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Field;
use App\Models\FieldsValue;
use App\Models\Nomenclature;
use App\Models\Whitelist;
use App\Models\TestResult;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ParseMain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $nomenclatures;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param Nomenclature $nomenclatureModel
     * @param TestResult $testResultModel
     */
    public function handle(Nomenclature $nomenclatureModel, TestResult $testResultModel)
    {
        $testResult = $testResultModel::create([
           'name' => 'Парсим шины диски',
            'parsed_items_number' => 0,
            'parsed_fields_number' => 0,
            'fields_number' => 0,
        ]);
        $nomenclatures = $nomenclatureModel->all();
        $fieldsNumber = [
            0 => 10,
            1 => 12
        ];
        $testResult->{'parsed_items_number'} = $nomenclatures->count();
        foreach($nomenclatures as $key => $nomenclature){
            $string = $nomenclature->{'source_string'};
            $groupId = 1;
            if(preg_match('~(?:диск| DIA[\s\d]| ET[\s\d])~i', $string)){
                $groupId = 2;
            }
            $result = $this->parse($string, $groupId);
            if($result['manufacturer']){
                $testResult->{'fields_number'} += $fieldsNumber[$groupId-1];

                $result['manufacturer']['status']?$testResult->{'parsed_fields_number'}++:null;
                $result['model']['status']?$testResult->{'parsed_fields_number'}++:null;
                foreach ($result['fields'] as $item){
                    $item['status']?$testResult->{'parsed_fields_number'}++:null;
                }
            }
            dump('Запарисл '.($key + 1).' строку');
            $testResult->save();

        }

//        dd('Количество элементов парсинга: '.$nomenclatures->count(), 'Получены значения для '.$parsedFields.' полей из '.$allFieldsNumber. ' ('.(($parsedFields/$allFieldsNumber)*100).'%)');
    }
    protected function parse($sourceString, $groupId)
    {
        $sourceString = preg_replace('~,~', '.', $sourceString);
        $response['sourceString'] = $sourceString;
        $fieldModel = new Field;
        $fields = $fieldModel->where('group', $groupId)->get();
        if($groupId === 1){
            $parseSetting = [
                [
                    'regExpMask' => '~_[/x\*]~i',
                    'doNotTouchSourceString' => true
                ],
                ['regExpMask' => '~ [0-9]+[/x\*]_~i'], ['regExpMask' => '~[R/] *_~'], ['regExpMask' => '~\s(?<toDelete>_)\w{0,1} *~'], ['regExpMask' => '~\s_(?:\s|$)~i'],
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
        $response['manufacturer'] = $this->parseCategory($sourceString, $groupId);
        $response['model'] = $this->parseCategory($sourceString, $response['manufacturer']['id']);
        $response['status'] = !preg_match('~\w~', $sourceString) ? true : false;
        return $response;
    }

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
        $categories = $categoryModel::search(strtolower($sourceString))->where('category_parent_id', $parent_category)->get();
        foreach ($categories as $category) {

            if (preg_match('~' . preg_quote($category{'name_ru-RU'}, '~') . '~ui', $sourceString, $m)) {
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
            if (preg_match('~' . preg_quote($item{'string'}, '~') . '~ui', $sourceString, $m)) {
                $result = $categoryModel->find($item->{'whitelisted_id'});
                if ((!$parent_category and ($result->{'category_parent_id'} === 1 or $result->{'category_parent_id'} === 2)) or $result->{'category_parent_id'} === $parent_category) {
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


        if(preg_match($regExp, $sourceString, $m)){
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

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
     * Execute the job.
     *
     * @return void
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
        $fieldsNumber = 7;

        foreach($nomenclatures as $nomenclature){
            $testResult->{'fields_number'} += $fieldsNumber;
            $string = $nomenclature->{'source_string'};
            $result = $this->parse($string);
            $result['manufacturer']['status']?$testResult->{'parsed_fields_number'}++:null;
            $result['model']['status']?$testResult->{'parsed_fields_number'}++:null;
            foreach ($result['fields'] as $item){
                $item['status']?$testResult->{'parsed_fields_number'}++:null;
            }
        }
        $testResult->{'parsed_items_number'} = $nomenclatures->count();
        $testResult->save();
//        dd('Количество элементов парсинга: '.$nomenclatures->count(), 'Получены значения для '.$parsedFields.' полей из '.$allFieldsNumber. ' ('.(($parsedFields/$allFieldsNumber)*100).'%)');
    }
    protected function parse($sourceString)
    {
        $sourceString = preg_replace('~,~', '.', $sourceString);
        $response['sourceString'] = $sourceString;
        $widthAndHeight = $this->parseWidthAndHeight($sourceString, 1, 2);
        $response['fields']['width'] = $widthAndHeight['width'];
        $response['fields']['height'] = $widthAndHeight['height'];
        $response['fields']['radius'] = $this->parseRadius($sourceString, 3);
        $response['fields']['carryingCapacityIndex'] = $this->parseCarryingCapacityIndex($sourceString, 4);
        $response['fields']['speedIndex'] = $this->parseSpeedIndex($sourceString, 5);
        $response['manufacturer'] = $this->parseCategory($sourceString);
        $response['model'] = $this->parseCategory($sourceString, $response['manufacturer']['id']);
        $response['status'] = !preg_match('~\w~', $sourceString) ? true : false;
        return $response;
    }

    /**
     * @param string $sourceString
     * @param int $parent_category
     * @return array
     */
    protected function parseCategory(string &$sourceString, $parent_category = 0)
    {
        $response = [
            'id' => 0,
            'displayName' => '',
            'status' => false
        ];
        $categoryModel = new Category;
//        dd($sourceString);

        $categories = $categoryModel::search($sourceString)->where('category_parent_id', $parent_category ? $parent_category : 1)->get();
        foreach ($categories as $category) {

            if (preg_match('~' . $category{'name_ru-RU'} . '~i', $sourceString, $m)) {
                if (!$parent_category or $category->{'category_parent_id'} === $parent_category) {
                    $response['id'] = $category->{'category_id'};
                    $response['displayName'] = $category->{'name_ru-RU'};
                    $response['status'] = true;
                    $sourceString = str_replace($m[0], '', $sourceString);
                }
            }
        }
        $whitelist = Whitelist::search($sourceString)->get();
        foreach ($whitelist as $item) {
            if (preg_match('~' . $item{'string'} . '~i', $sourceString, $m)) {
                $result = $categoryModel->find($item->{'whitelisted_id'});
                if (!$parent_category or $result->{'category_parent_id'} === $parent_category) {
                    $response['id'] = $result->{'category_id'};
                    $response['displayName'] = $result->{'name_ru-RU'};
                    $response['status'] = true;
                    $sourceString = str_replace($m[0], '', $sourceString);
                }
            }
        }
        return $response;
    }

    /**
     * @param string $sourceString
     * @param int $widthFieldId
     * @param int $heightFieldId
     * @return array
     */
    protected function parseWidthAndHeight(&$sourceString, $widthFieldId, $heightFieldId)
    {
        $fieldModel = new Field;
        $fieldsValueModel = new FieldsValue;
        $response = [
            'width' => [
                'fieldId' => $widthFieldId,
                'fieldsValueId' => 0,
                'displayValue' => '',
                'fieldName' => '',
                'status' => false,
            ],
            'height' => [
                'fieldId' => $heightFieldId,
                'fieldsValueId' => 0,
                'displayValue' => '',
                'fieldName' => '',
                'status' => false,
            ]
        ];
        $widthField = $fieldModel->where('field_id', '=', $widthFieldId)->first();
        $response['width']['fieldName'] = $widthField->{'name_ru-RU'};
        $response['width']['fieldName'] = $widthField->{'name_ru-RU'};
        $heightField = $fieldModel->where('field_id', '=', $heightFieldId)->first();
        $response['height']['fieldName'] = $heightField->{'name_ru-RU'};
        $regExp = '~' . $widthField->prepareForRegExp() . '[/x\*]' . $heightField->prepareForRegExp() . '~';
        if (preg_match($regExp, $sourceString, $m)) {
            $response['width']['displayValue'] = $width = $m['field' . $widthFieldId];
            $response['width']['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $widthFieldId],
                ['name_ru-RU', '=', $response['width']['displayValue']]
            ])->first()->{'fields_value_id'};
            $response['width']['status'] = true;


            $response['height']['displayValue'] = $width = $m['field' . $heightFieldId];
            $response['height']['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $heightFieldId],
                ['name_ru-RU', '=', $response['height']['displayValue']]
            ])->first()->{'fields_value_id'};
            $response['height']['status'] = true;
            $sourceString = str_replace($m[0], '', $sourceString);
        }
        return $response;
    }

    /**
     * @param $sourceString
     * @param $radiusFieldId
     * @return array
     */
    protected function parseRadius(&$sourceString, $radiusFieldId)
    {
        $fieldModel = new Field;
        $fieldsValueModel = new FieldsValue;
        $response = [
            'fieldId' => $radiusFieldId,
            'fieldsValueId' => 0,
            'displayValue' => '',
            'fieldName' => '',
            'status' => false,
        ];
        $radiusField = $fieldModel->where('field_id', '=', $radiusFieldId)->first();
        $response['fieldName'] = $radiusField->{'name_ru-RU'};
        $regExp = '~R' . $radiusField->prepareForRegExp() . '~';
        if (preg_match($regExp, $sourceString, $m)) {
            $response['displayValue'] = $m['field' . $radiusFieldId];
            $response['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $radiusFieldId],
                ['name_ru-RU', '=', $response['displayValue']]
            ])->first()->{'fields_value_id'};
            $response['status'] = true;
            $sourceString = str_replace($m[0], '', $sourceString);
        }
        return $response;
    }

    /**
     * @param $sourceString
     * @param $carryingCapacityIndexFieldId
     * @return array
     */
    protected function parseCarryingCapacityIndex(&$sourceString, $carryingCapacityIndexFieldId)
    {
        $fieldModel = new Field;
        $fieldsValueModel = new FieldsValue;
        $response = [
            'fieldId' => $carryingCapacityIndexFieldId,
            'fieldsValueId' => 0,
            'displayValue' => '',
            'fieldName' => '',
            'status' => false,
        ];
        $carryingCapacityIndexField = $fieldModel->where('field_id', '=', $carryingCapacityIndexFieldId)->first();
        $response['fieldName'] = $carryingCapacityIndexField->{'name_ru-RU'};
        $regExp = '~' . $carryingCapacityIndexField->prepareForRegExp() . '~';
        if (preg_match($regExp, $sourceString, $m)) {
            $response['displayValue'] = $m['field' . $carryingCapacityIndexFieldId];
            $response['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $carryingCapacityIndexFieldId],
                ['name_ru-RU', '=', $response['displayValue']]
            ])->first()->{'fields_value_id'};
            $response['status'] = true;
            $sourceString = str_replace($m[0], '', $sourceString);
        }
        return $response;
    }

    protected function parseSpeedIndex(&$sourceString, $speedIndexFieldId)
    {
        $fieldModel = new Field;
        $fieldsValueModel = new FieldsValue;
        $response = [
            'fieldId' => $speedIndexFieldId,
            'fieldsValueId' => 0,
            'displayValue' => '',
            'fieldName' => '',
            'status' => false,
        ];
        $speedIndexField = $fieldModel->where('field_id', '=', $speedIndexFieldId)->first();
        $response['fieldName'] = $speedIndexField->{'name_ru-RU'};
        $regExp = '~' . $speedIndexField->prepareForRegExp() . '$~i';
        if (preg_match($regExp, $sourceString, $m)) {
            $response['displayValue'] = $m['field' . $speedIndexFieldId];
            $response['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $speedIndexFieldId],
                ['name_ru-RU', '=', $response['displayValue']]
            ])->first()->{'fields_value_id'};
            $response['status'] = true;
            $sourceString = str_replace($m[0], '', $sourceString);
        }
        return $response;
    }
}

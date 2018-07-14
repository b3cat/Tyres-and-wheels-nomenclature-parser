<?php

namespace App\Models\Parser;


use App\Models\Category;
use App\Models\Field;
use App\Models\FieldsValue;

class Parser
{
    protected $parameters;

    public function __construct()
    {
        $this->parameters = [
            1 => [
                'manufacturerWhitelists' => Category::whitelists(1),
                'fields' => Field::all()->where('group', 1),
            ],
            2 => [
                'manufacturerWhitelists' => Category::whitelists(2),
                'fields' => Field::all()->where('group', 2),
            ]
        ];
    }
    protected function findGroupId($sourceString){
        $groupId = 1;
        if (preg_match('~(?:диск| DIA[\s\d]| ET[\s\d])~ui', $sourceString)) {
            $groupId = 2;
        }
        return $groupId;
    }
    public function parse($sourceString)
    {
        $groupId = $this->findGroupId($sourceString);
        $parameters = $this->parameters[$groupId];
        $sourceString = preg_replace('~,~', '.', $sourceString);
        $categoryModel = new Category;
        $response['sourceString'] = $sourceString;
        $sourceString = mb_strtolower($sourceString);
        $response['manufacturer'] = $this->parseCategory($sourceString, $parameters['manufacturerWhitelists']);
        $modelWhitelists = Category::whitelists($response['manufacturer']['id']);
        $response['model'] = $this->parseCategory($sourceString, $modelWhitelists);
        $fields = $parameters['fields'];
        foreach ($fields as $currentFieldId => $field) {
            $response['fields'][$field->{'name_ru-RU'}] = $this->parseField($sourceString,
                $field,
                isset($parameters['parseSettings'][$currentFieldId]['doNotTouchSourceString']) ? false : true);
        }
        if ($response['model']['id']) {
//            $fieldsFromCategory = $categoryModel
//                ->where('category_id', '=', $response['model']['id'])
//                ->first();
            $fieldsFromCategory = $categoryModel->where('category_id', $response['model']['id'])->first();

            $fieldsFromCategory = json_decode($fieldsFromCategory->{'short_description_ru-RU'});
            if (is_object($fieldsFromCategory)) {
                foreach ($fieldsFromCategory as $fieldId => $fieldValueId) {
                    if (null !== $fields->where('field_id', $fieldId)->first()) {
                        $name = $fields->where('field_id', $fieldId)->first()->{'name_ru-RU'};
                        $response['fields'][$name] = [
                            'fieldId' => +$fieldId,
                            'fieldsValueId' => !is_null($fieldValueId) ? $fieldValueId : 0,
                            'displayValue' => '',
                            'fieldName' => $name,
                            'status' => true,
                        ];
                    }

                }

            }
        }

        $response['status'] = !preg_match('~\w~', $sourceString) ? true : false;
        return $response;
    }

    /**
     * @param string $sourceString
     * @param $whitelists
     * @return array|bool
     */
    protected function parseCategory(&$sourceString, $whitelists)
    {
        $response = [
            'id' => 0,
            'displayName' => '',
            'status' => false
        ];

        $match = '';
        foreach ($whitelists as $manufacturerId => $whitelist) {
            foreach ($whitelist as $item) {
                if (
                    (mb_stripos($sourceString, $item) !== false)
                    && (mb_strlen($item) > mb_strlen($response['displayName']))
                ) {
                    dump($item);
                    $match = $item;
                    $response = [
                        'id' => $manufacturerId,
                        'displayName' => $item,
                        'status' => true
                    ];
                }
            }
        }
        if (strlen($response['displayName']) > 0) {
            $sourceString = str_ireplace($match, '', $sourceString);
        }

        if (!$response['id']) return false;
        return $response;
    }

    /**
     * @param string $sourceString
     * @param Field $field
     * @return array
     */
    protected function parseField(&$sourceString, $field)
    {
        $response = [
            'fieldId' => $field->{'field_id'},
            'fieldsValueId' => 0,
            'displayValue' => '',
            'fieldName' => $field->{'name_ru-RU'},
            'status' => false,
        ];
        $modifySourceString = $field->{'regExpMask'}->{'reg_exp_mask'}{0};
        $modifySourceString = $modifySourceString === 'F' ? false : true;
        if(!$modifySourceString){
            $regExpMask = mb_substr($field->{'regExpMask'}->{'reg_exp_mask'}, 1);
        } else {
            $regExpMask = $field->{'regExpMask'}->{'reg_exp_mask'};
        }
        $regExp = str_replace('_', $field->prepareForRegExp(), $regExpMask);
        dump($regExp);

//        dump($sourceString, $regExp);
        $fieldsValueModel = new FieldsValue;
        if (preg_match($regExp, $sourceString, $m)) {
            $response['displayValue'] = $m['field' . $field->{'field_id'}];
            $response['fieldsValueId'] = $fieldsValueModel->where([
                ['field_id', '=', $field->{'field_id'}],
                ['name_ru-RU', '=', $response['displayValue']]
            ])->first()->{'fields_value_id'};
            $response['status'] = true;
            if ($modifySourceString) {
                $sourceString = str_replace(isset($m['toDelete']) ? $m['toDelete'] : $m[0], '', $sourceString);
            }
        }
        return $response;
    }
}

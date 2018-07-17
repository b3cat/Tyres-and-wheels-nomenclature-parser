<?php

namespace App\Models\Parser;


use App\Models\Category;
use App\Models\Field;
use App\Models\FieldsValue;

class Parser
{
    protected $parameters;
    /**
     * @var Category $category
     */
    protected $category;
    /**
     * @var FieldsValue $fieldsValue
     */
    protected $fieldsValue;

    public function __construct(Category $category, FieldsValue $fieldsValue)
    {
        $this->category = $category;
        $this->fieldsValue = $fieldsValue;
    }

    /**
     * @param $sourceString
     * @return int
     */
    protected function findGroupId($sourceString)
    {
        $mainCategories = $this->category->mainCategories();
        //тут надо тоже унивирсалить, но не могу придумать регулярку для шин пока, так что они по дефолту стоят
        $groupId = 1;
        foreach ($mainCategories as $mainCategory) {
            /**
             * @var Category $mainCategory
             */
            $regExp = $mainCategory->regExp->regExpValue->reg_exp;
            if (preg_match($regExp, $sourceString)) {
                $groupId = $mainCategory->{'category_id'};
            }
        }
        return $groupId;
    }

    public function parse($sourceString)
    {
        $groupId = $this->findGroupId($sourceString);
        $sourceString = preg_replace('~,~', '.', $sourceString);
        $response['sourceString'] = $sourceString;
        $sourceString = mb_strtolower($sourceString);
        $response['manufacturer'] = $this->parseCategory($sourceString, $this->category::whitelists($groupId));
        $modelWhitelists = $this->category::whitelists($response['manufacturer']['id']);
        $response['model'] = $this->parseCategory($sourceString, $modelWhitelists);
        $fields = $this->category->where('category_id', $groupId)->first()->fieldsWithPairs();
        foreach ($fields as $currentFieldId => $field) {
            /**
             * @var Field $field
             */
            $fieldResponse = $this->parseField($sourceString, $field);
            if ($field->isPairField()) {
                $response['fields'][$field->{'name_ru-RU'}] = $fieldResponse[0];
                $response['fields'][$field->{'pairField'}->{'name_ru-RU'}] = $fieldResponse[1];
            } else {
                $response['fields'][$field->{'name_ru-RU'}] = $fieldResponse;
            }
        }
        if ($response['model']['id']) {
//            $fieldsFromCategory = $categoryModel
//                ->where('category_id', '=', $response['model']['id'])
//                ->first();
            $fieldsFromCategory = $this->category->where('category_id', $response['model']['id'])->first();

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
//        dd($sourceString);
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
                    ($item !== '')
                    && (mb_stripos($sourceString, $item) !== false)
                    && (mb_strlen($item) > mb_strlen($response['displayName']))
                ) {
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
//пока не нашел, как без регулярок удалить только первое вхождение
            $sourceString = preg_replace('~' . $match . '~ui', '', $sourceString, 1);
//            $sourceString = str_ireplace($match, '', $sourceString);
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
        $isPair = $field->isPairField();
        if (!$isPair) {
            $response = [
                'fieldId' => $field->{'field_id'},
                'fieldsValueId' => 0,
                'displayValue' => '',
                'fieldName' => $field->{'name_ru-RU'},
                'status' => false,
            ];
        } else {
            $response = [
                [
                    'fieldId' => $field->{'field_id'},
                    'fieldsValueId' => 0,
                    'displayValue' => '',
                    'fieldName' => $field->{'name_ru-RU'},
                    'status' => false,
                ],
                [
                    'fieldId' => $field->{'pairField'}->{'field_id'},
                    'fieldsValueId' => 0,
                    'displayValue' => '',
                    'fieldName' => $field->{'pairField'}->{'name_ru-RU'},
                    'status' => false,
                ]
            ];
        }
        $regExps = $field->{'regExpMasks'}->sortBy('priority');
        foreach ($regExps as $regExp) {
//            dump($sourceString);

            $modifySourceString = $regExp->{'reg_exp_mask'}{0};
            $modifySourceString = $modifySourceString === 'F' ? false : true;
            if (!$modifySourceString) {
                $regExpMask = mb_substr($regExp->{'reg_exp_mask'}, 1);
            } else {
                $regExpMask = $regExp->{'reg_exp_mask'};
            }
            $regExpForParse = str_replace('_', $field->prepareForRegExp(), $regExpMask);

            if ($isPair) {
                $regExpForParse = str_replace('@', $field->{'pairField'}->prepareForRegExp(), $regExpForParse);
            }
            if (preg_match($regExpForParse, $sourceString, $m)) {
                if ($isPair) {
                    $response[0]['displayValue'] = $m['field' . $field->{'field_id'}];
                    $response[0]['fieldsValueId'] = $this->fieldsValue->where([
                        ['field_id', '=', $field->{'field_id'}],
                        ['name_ru-RU', '=', $response[0]['displayValue']]
                    ])->first()->{'fields_value_id'};
                    $response[0]['status'] = true;

                    $response[1]['displayValue'] = $m['field' . $field->{'pairField'}->{'field_id'}];
                    $response[1]['fieldsValueId'] = $this->fieldsValue->where([
                        ['field_id', '=', $field->{'pairField'}->{'field_id'}],
                        ['name_ru-RU', '=', $response[1]['displayValue']]
                    ])->first()->{'fields_value_id'};
                    $response[1]['status'] = true;
                } else {
                    $response['displayValue'] = $m['field' . $field->{'field_id'}];
                    $response['fieldsValueId'] = $this->fieldsValue->where([
                        ['field_id', '=', $field->{'field_id'}],
                        ['name_ru-RU', '=', $response['displayValue']]
                    ])->first()->{'fields_value_id'};
                    $response['status'] = true;
                }
                if ($modifySourceString) {
                    $sourceString = str_replace(isset($m['toDelete']) ? $m['toDelete'] : $m[0], '', $sourceString);
                }
                break;
            }
        }
        return $response;
    }
}

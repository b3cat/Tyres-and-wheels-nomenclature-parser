<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Field;
use App\Models\FieldsValue;
use App\Models\Nomenclature;
use App\Models\Parser\Parser;
use App\Models\Product;
use App\Models\ProductField;
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
    public function handle(Nomenclature $nomenclatureModel, Parser $parser, TestResult $testResultModel, Product $productModel, ProductField $productFieldModel, Category $categoryModel)
    {
        $nomenclatures = $nomenclatureModel->inRandomOrder()->limit(1000)->get();
        foreach ($nomenclatures as $key => $nomenclature) {
            $string = $nomenclature->{'source_string'};
            $result = $parser->parse($string);
            $product = $productModel::create([
                'category_id' => !is_null($result['model']['id']) ? $result['model']['id'] : 0,
                'source_string' => $result['sourceString'],
                'errors' => false,
                'non_critical_errors' => !$result['status']
            ]);
//
            foreach ($result['fields'] as $item) {
                if (!isset($fieldsFromCategory[$item['fieldId']])) {
                    $productFieldModel->create([
                        'field_id' => $item['fieldId'],
                        'fields_value_id' => $item['fieldsValueId'],
                        'product_id' => $product->{'id'}

                    ]);
                    if (($item['fieldsValueId'] === 0) and ($item['fieldId'] !== 9)) {
                        $product->{'errors'} = true;
                    }
                }
            }

            $product->save();
            dump('Запарисл ' . ($key + 1) . ' строку');
        }


//        dd('Количество элементов парсинга: '.$nomenclatures->count(), 'Получены значения для '.$parsedFields.' полей из '.$allFieldsNumber. ' ('.(($parsedFields/$allFieldsNumber)*100).'%)');
    }


}

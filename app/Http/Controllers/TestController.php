<?php

namespace App\Http\Controllers;

use App\Jobs\ParseCategories;
use App\Models\Category;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * @param Category $categoryModel
     */
    function testManufacturers(Category $categoryModel)
    {
//        ParseCategories::dispatchNow();
        $tyresManufacturers = $categoryModel->where('category_parent_id', '=', '1')->get();
        echo '<h1>Производители шин:</h1>';
        foreach ($tyresManufacturers  as $manufacturer){
            echo '<a href="/manufacturers/'.$manufacturer->{'category_id'}.'/models">'.$manufacturer->{'name_ru-RU'}.'</a><br>';
        }
        $wheelManufacturers = $categoryModel->where('category_parent_id', '=', '2')->get();
        echo '<h1>Производители дисков:</h1>';
        foreach ($wheelManufacturers  as $manufacturer){
            echo '<a href="/manufacturers/'.$manufacturer->{'category_id'}.'/models">'.$manufacturer->{'name_ru-RU'}.'</a><br>';
        }
    }

    /**
     * @param integer $id
     * @param Category $categoryModel
     */
    function testModels($id, Category $categoryModel){
        $manufacturer = $categoryModel->where('category_id', '=', $id)->first();

        echo '<h1>Модели '.$manufacturer->{'name_ru-RU'}.'</h1>';
        $models = $categoryModel->where('category_parent_id', '=', $id)->get();
        foreach ($models  as $model){
//            dd($model->allParentCategories);
            echo $model->{'name_ru-RU'}.'<br>';
        }
    }
}

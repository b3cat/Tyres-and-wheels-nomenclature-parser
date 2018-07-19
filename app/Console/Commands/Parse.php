<?php

namespace App\Console\Commands;

use App\Jobs\ParseCategories;
use App\Jobs\ParseExtraFields;
use App\Jobs\ParseExtraFieldsValues;
use App\Jobs\ParseMain;
use App\Jobs\ParseNomenclatures;
use App\Models\Category;
use App\Models\Field;
use Illuminate\Console\Command;

class parse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse {target}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var Category
     */
    protected $categories;

    /**
     * @var Field
     */
    protected $fields;
    /**
     * parse constructor.
     * @param Category $categories
     * @param Field $fields
     */
    public function __construct(Category $categories, Field $fields)
    {
        parent::__construct();
        $this->categories = $categories;
        $this->fields = $fields;
    }

    /**
     *
     */
    public function handle()
    {
        $target = $this->argument('target');
        $this->info('The target for parsing is '.$target);

        switch ($target){
            case 'manufacturers':
                ParseCategories::dispatch('https://admin.shiniplus.ru/api/manufacturers/');
                break;
            case 'models':
                $categories = $this->categories->all();
                foreach ($categories as $category){
                    ParseCategories::dispatchNow('https://admin.shiniplus.ru/api/manufacturers/'.$category->{'category_id'});
                }
                break;
            case 'fields':
                ParseExtraFields::dispatchNow('https://admin.shiniplus.ru/api/extrafields/');
                break;
            case 'fields-values':
                $fields = $this->fields->all();
                foreach ($fields as $field){
                    ParseExtraFieldsValues::dispatch('https://admin.shiniplus.ru/api/extrafields/'.$field->{'field_id'});
                }
                break;
            case 'nomenclatures':
                ParseNomenclatures::dispatchNow('https://admin.shiniplus.ru/api/nomenclature/');
                break;
            case 'main':
                ParseMain::dispatchNow();
                break;

        }
    }
}

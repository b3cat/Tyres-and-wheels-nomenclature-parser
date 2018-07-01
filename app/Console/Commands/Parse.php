<?php

namespace App\Console\Commands;

use App\Jobs\ParseCategories;
use App\Models\Category;
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
     * parse constructor.
     * @param Category $categories
     */
    public function __construct(Category $categories)
    {
        $this->categories = $categories;
        parent::__construct();
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
                ParseCategories::dispatch('https://admin.shiniplus.ru/api/manufacturers');
                break;
            case 'models':
                $categories = $this->categories->all();
                foreach ($categories as $category){
                    ParseCategories::dispatch('https://admin.shiniplus.ru/api/manufacturers/'.$category->{'category_id'});
                }
                break;
        }
    }
}

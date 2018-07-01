<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;
use App\Models\Category;

class ParseCategories implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $apiUrl;
    /**
     * @var Category
     */
    protected $categories;

    /**
     * ParseCategories constructor.
     * @param $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;


    }

    /**
     * @param Category $categories
     */
    public function handle(Category $categories)
    {
        $this->categories = $categories;
        $this->parse($this->apiUrl);
    }

    /**
     * @param string $url
     */
    public function parse($url)
    {
        $client = new Client();
        $res = $client->request('GET', $url);
        $resBody = json_decode($res->getBody());
        $nextPage = $resBody->next_page_url ?? null;
        $categories = $resBody->data;

        foreach ($categories as $category) {
            $name = $category->{'name_ru-RU'};
            $categoryId = $category->{'category_id'};
            $categoryPrentId = $category->{'category_parent_id'};

            $category = $this->categories::firstOrCreate([
                'category_id' => $categoryId,
            ]);
            $category->{'name_ru-RU'} = $name;
            $category->{'category_parent_id'} = $categoryPrentId;
            $category->save();
        }
        if ($nextPage) {
            $this->parse($nextPage);
        }
    }
}

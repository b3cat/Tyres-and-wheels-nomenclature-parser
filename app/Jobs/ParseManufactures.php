<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Manufacture;

class ParseManufactures implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $api_url;
    /**
     * @var Manufacture
     */
    protected $manufactures;

    /**
     * ParseManufactures constructor.
     */
    public function __construct()
    {
        $this->api_url = 'https://admin.shiniplus.ru/api/manufacturers';


    }

    /**
     * @param Manufacture $manufactures
     */
    public function handle(Manufacture $manufactures)
    {
        $this->manufactures = $manufactures;
        $this->parse($this->api_url);
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
        $manufactures = $resBody->data;

        foreach ($manufactures as $manufacture) {
            $name = $manufacture->{'name_ru-RU'};
            $categoryId = $manufacture->{'category_id'};

            $manufacture = $this->manufactures::firstOrCreate([
                'category_id' => $categoryId,
            ]);
            $manufacture->{'name_ru-RU'} = $name;
            $manufacture->save();
        }
        if ($nextPage) {
            $this->parse($nextPage);
        }
    }
}

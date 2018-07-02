<?php

namespace App\Jobs;

use App\Models\FieldsValue;
use App\Models\Nomenclature;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ParseNomenclatures implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var string
     */
    protected $apiUrl;
    /**
     * @var Nomenclature
     */
    protected $nomenclatures;
    /**
     * ParseExtraFields constructor.
     * @param $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param Nomenclature $nomenclatures
     */
    public function handle(Nomenclature $nomenclatures)
    {
        $this->nomenclatures = $nomenclatures;
        $this->parse($this->apiUrl);
    }
    public function parse($url)
    {
        $client = new Client();
        $res = $client->request('GET', $url);
        $resBody = json_decode($res->getBody());
        $nextPage = $resBody->next_page_url ?? null;
        $nomenclatures = $resBody->data;
        foreach ($nomenclatures as $value){
            $string = $value->{'source_string'};
            $this->nomenclatures::firstOrCreate([
                'source_string' => $string,
            ]);
        }
        if ($nextPage) {
            $this->parse($nextPage);
        }
    }
}

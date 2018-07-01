<?php

namespace App\Jobs;

use App\Models\FieldsValue;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ParseExtraFieldsValues implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var string
     */
    protected $apiUrl;
    /**
     * @var FieldsValue
     */
    protected $fieldsValues;
    /**
     * ParseExtraFields constructor.
     * @param $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param FieldsValue $fieldsValues
     */
    public function handle(FieldsValue $fieldsValues)
    {
        $this->fieldsValues = $fieldsValues;
        $this->parse($this->apiUrl);
    }
    public function parse($url)
    {
        $client = new Client();
        $res = $client->request('GET', $url);
        $resBody = json_decode($res->getBody());
        $nextPage = $resBody->next_page_url ?? null;
        $fieldsValues = $resBody->data;
        foreach ($fieldsValues as $value){
            $name = $value->{'name_ru-RU'};
            $id = $value->{'id'};
            $fieldId = $value->{'field_id'};
            $fieldsValue = $this->fieldsValues::firstOrCreate([
                'fields_value_id' => $id,
            ]);
            $fieldsValue->{'name_ru-RU'} = $name;
            $fieldsValue->{'field_id'} = $fieldId;
            $fieldsValue->save();

        }
        if ($nextPage) {
            $this->parse($nextPage);
        }
    }
}

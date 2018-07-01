<?php

namespace App\Jobs;

use App\Models\Field;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ParseExtraFields implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var string
     */
    protected $apiUrl;
    /**
     * @var Field
     */
    protected $fields;
    /**
     * ParseExtraFields constructor.
     * @param $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param Field $fields
     */
    public function handle(Field $fields)
    {
        $this->fields = $fields;
        $this->parse($this->apiUrl);
    }
    public function parse($url)
    {
        $client = new Client();
        $res = $client->request('GET', $url);
        $resBody = json_decode($res->getBody());
        $nextPage = $resBody->next_page_url ?? null;
        $fields = $resBody->data;
        foreach ($fields as $field){
            $name = $field->{'name_ru-RU'};
            $id = $field->{'id'};
            $group = $field->{'group'};
            $field = $this->fields::firstOrCreate([
                'field_id' => $id,
            ]);
            $field->{'name_ru-RU'} = $name;
            $field->{'group'} = $group;
            $field->save();

        }
        if ($nextPage) {
            $this->parse($nextPage);
        }
    }
}

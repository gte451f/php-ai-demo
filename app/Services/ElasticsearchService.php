<?php

namespace App\Services;

use Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])
            ->build();
    }

    public function search($index, $query)
    {
        $params = [
            'index' => $index,
            'body'  => $query,
        ];

        return $this->client->search($params);
    }

    public function index($index, $id, $data)
    {
        $params = [
            'index' => $index,
            'id'    => $id,
            'body'  => $data,
        ];

        return $this->client->index($params);
    }
}

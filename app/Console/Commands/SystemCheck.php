<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;
use LLPhant\Chat\OllamaChat;
use LLPhant\Embeddings\DataReader\FileDataReader;
use LLPhant\OllamaConfig;

class SystemCheck extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:system-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        echo "<pre>";

        # connect to Elastic Search
        // create and then remove an index
        try {
            $this->print('Testing Elastic Search...');
            $client = ClientBuilder::create()
                ->setHosts(['http://elasticsearch:9200'])
                ->build();

            // Define an index name
            $indexName = 'test-index';

            // 1. Create an index
            $createParams = [
                'index' => $indexName,
            ];
            $response = $client->indices()->create($createParams);
            $this->print("--Index created: " . json_encode($response));

            // 2. Check if the index exists
            $exists = $client->indices()->exists(['index' => $indexName]);
            $result = $exists ? "--Index exists: $indexName" : "--Index does not exist";
            $this->print($result);

            // 3. Delete the index
            $deleteParams = [
                'index' => $indexName,
            ];
            $response = $client->indices()->delete($deleteParams);
            $this->print("--Index deleted: " . json_encode($response));

        } catch (\Exception $e) {
            $this->print('-- Error accessing Elastic Search');
            $this->print($e->getMessage());
        }


        # Ollama with LlamaX.X
        try {
            $this->print('Testing Ollama...');
            $config = new OllamaConfig();
            $config->model = 'llama3.2:3b';
            $config->url = 'http://host.docker.internal:11434/api/';
            $config->modelOptions = [
                'options' => [
                    'temperature' => 0
                ]
            ];
            $chat = new OllamaChat($config);
            $question = 'what is one + one ?';
            $response = $chat->generateText($question); // will return something like "Two"
            $this->print("-- Question: " . $question);
            $this->print("-- Answer: " . $response);
        } catch (\Exception $e) {
            $this->print('-- Error checking Ollama');
            $this->print($e->getMessage());
        }

        # test file inputs
        # Read PDF file
        $this->print('Testing PDF File Access');
        $this->print('-- Reading the PDF File');
        $reader = new FileDataReader(storage_path('app/private/file-inputs/') . 'example.pdf');
        $documents = $reader->getDocuments();
        $this->print("-- Number of PDF files: " . count($documents));


        echo "</pre>";
    }

    public function print($message)
    {
        echo $message . PHP_EOL;
    }
}

<?php

namespace App\Logging;

use Elastic\Elasticsearch\ClientBuilder as ElasticsearchClientBuilder;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Monolog\Logger;

class KibanaHandler extends AbstractProcessingHandler
{
    protected $client;
    protected $index;
    protected $apiKey;
    protected $cloudId;
    protected $initialized = false;

    public function __construct($level, $bubble = true, $apiKey = null, $cloudId = null, $index = 'logs')
    {
        parent::__construct($level, $bubble);

        $this->apiKey = $apiKey;
        $this->cloudId = $cloudId;
        $this->index = $index;
    }

    protected function initializeClient()
    {
        if ($this->initialized) {
            return;
        }

        if (!$this->apiKey || !$this->cloudId) {
            error_log("Kibana logging: Missing API key or Cloud ID. API Key: " . ($this->apiKey ? 'present' : 'missing') . ", Cloud ID: " . ($this->cloudId ? 'present' : 'missing'));
            return;
        }

        try {
            $this->client = ElasticsearchClientBuilder::create()
                ->setElasticCloudId($this->cloudId)
                ->setApiKey($this->apiKey)
                ->build();
            
            $this->initialized = true;
        } catch (\Exception $e) {
            error_log("Kibana logging: Failed to initialize client - " . $e->getMessage());
        }
    }

    protected function write(LogRecord $record): void
    {
        $this->initializeClient();

        if (!$this->client) {
            return;
        }

        try {
            $this->client->index([
                'index' => $this->index,
                'body' => [
                    '@timestamp' => $record->datetime->format('c'),
                    'level' => $record->level->getName(),
                    'message' => $record->message,
                    'context' => $record->context,
                    'extra' => $record->extra,
                    'channel' => $record->channel,
                    'app_name' => 'Laravel',
                    'environment' => 'production',
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Kibana logging failed: " . $e->getMessage());
        }
    }
}

class KibanaLogger
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('kibana');
        
        $handler = new KibanaHandler(
            $config['level'] ?? \Monolog\Level::Debug,
            true,
            env('KIBANA_API_KEY'),
            env('KIBANA_CLOUD_ID'),
            env('KIBANA_INDEX', 'logs')
        );
        
        $logger->pushHandler($handler);
        
        return $logger;
    }
}

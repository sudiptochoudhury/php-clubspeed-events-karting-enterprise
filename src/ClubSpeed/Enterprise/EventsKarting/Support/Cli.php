<?php

namespace SudiptoChoudhury\ClubSpeed\Enterprise\EventsKarting\Support;

use SudiptoChoudhury\Support\Abstracts\AbstractCli;
use splitbrain\phpcli\Options;

use SudiptoChoudhury\ClubSpeed\Enterprise\EventsKarting\Api;

class Cli extends AbstractCli
{

    public static $rootPath = __DIR__;
    protected $versionName = 'version 0.0.1';
    protected $welcome = 'ClubSpeed Events Karting (Enterprise) API CLI tool';
    protected $apiProvider = 'ClubSpeed';

    protected $apiServiceDefPath = 'src/ClubSpeed/Enterprise/EventsKarting/config/csek-ent.json';
    protected $apiService = [];
    protected $config = [];
    protected $commandOptions = []; // API options


    protected $configFile = 'csek-ent.json';
    protected $log = null;
    protected $env = 'dev';
    protected $simulate = false;
    protected $quiet = false;
    protected $skipDefaults = false;
    protected $logdefault = 'debug';

    protected $moreCommands = [
        'gendef' => [
            'help' => 'Generate and show Service Definition for get/post/put/delete operations',
            'options' => [['operation', 'Operation Name', 'N', true]],
        ],
    ];

    public function gendef($options)
    {

        extract($options);
        /** @var  $operation */
        $operationTitleCase = ucfirst($operation);

        $json = "
        \"get{$operationTitleCase}\": {
            \"extends\": \"_withAdditionalQueryParameters\",
            \"httpMethod\": \"GET\",
            \"description\": \"Get {$operationTitleCase}\",
            \"uri\": \"{$operation}/{id}\",
            \"responseModel\": \"getResponse\",
            \"parameters\": {
                \"id\": {
                    \"location\": \"uri\"
                }
            }
        },
        \"create{$operationTitleCase}\": {
            \"extends\": \"_withAdditionalJSONParameters\",
            \"httpMethod\": \"POST\",
            \"description\": \"Create {$operationTitleCase}\",
            \"uri\": \"{$operation}\",
            \"responseModel\": \"getResponse\",
            \"parameters\": {
            }
        },
        \"update{$operationTitleCase}\": {
            \"extends\": \"_withAdditionalJSONParameters\",
            \"httpMethod\": \"PUT\",
            \"description\": \"Update {$operationTitleCase}\",
            \"uri\": \"{$operation}/{id}\",
            \"responseModel\": \"getResponse\",
            \"parameters\": {
                \"id\": {
                    \"location\": \"uri\",
                    \"type\": [\"integer\", \"string\"],
                    \"required\": true
                }
            }
        },
        \"delete{$operationTitleCase}\": {
            \"extends\": \"_withAdditionalJSONParameters\",
            \"httpMethod\": \"DELETE\",
            \"description\": \"Delete {$operationTitleCase}\",
            \"uri\": \"{$operation}/{id}\",
            \"responseModel\": \"getResponse\",
            \"parameters\": {
                \"id\": {
                    \"location\": \"uri\",
                    \"required\": true
                }
            }
        },
        \"get{$operationTitleCase}Count\": {
            \"extends\": \"_withAdditionalQueryParameters\",
            \"httpMethod\": \"GET\",
            \"description\": \"Get {$operationTitleCase}s count\",
            \"uri\": \"{$operation}/count\",
            \"responseModel\": \"getResponse\",
            \"parameters\": {
            }
        },
        ";

        echo $json;
    }

    /**
     * @param $request \GuzzleHttp\Psr7\Request
     *
     * @return mixed
     */
    public function requestHandler($request)
    {
        if (!$this->quiet) {
            $content = (string)$request->getBody();
            $this->debug("\n\nREQUEST: \n$content\n\n");
        }
        return $request;
    }

    /**
     * @param $response \GuzzleHttp\Psr7\Response
     *
     * @return mixed
     */
    public function responseHandler($response)
    {
        if (!$this->quiet) {
            $content = (string)$response->getBody();
            $this->debug("\n\nRESPONSE: \n$content\n\n");
        }
        return $response;
    }

    public function logResult($result)
    {
        if (!$this->quiet) {
            if ($result['code'] ?? -1 == -1) {
                $this->success($result);
            } else {
                $this->error($result);
            }
        }
    }

    protected function callApi($command, Options $options)
    {
        $apiConfig = $this->config;
        $this->configureLog($apiConfig, $command, $options);

        $apiConfig['settings'] = $apiConfig['settings'] ?? [];
        $apiConfig['settings']['requestHandler'] = [$this, 'requestHandler'];
        $apiConfig['settings']['responseHandler'] = [$this, 'responseHandler'];

        $api = new Api($apiConfig);
        $payload = $this->getRequestPayload($command, $options);

        $data = $api->$command($payload);

        $this->logResult($data);

        return $data;
    }

}
<?php

namespace SudiptoChoudhury\ClubSpeed\RentalKarting\Support;

use SudiptoChoudhury\Support\Abstracts\AbstractCli;
use splitbrain\phpcli\Options;

use SudiptoChoudhury\ClubSpeed\RentalKarting\Api;

class Cli extends AbstractCli
{

    public static $rootPath = __DIR__;
    protected $versionName = 'version 0.0.1';
    protected $welcome = 'ClubSpeed Rental Karting API CLI tool';
    protected $apiProvider = 'ClubSpeed';

    protected $apiServiceDefPath = 'src/ClubSpeed/RentalKarting/config/csrk.json';
    protected $apiService = [];
    protected $config = [];
    protected $commandOptions = []; // API options


    protected $configFile = 'csrk.json';
    protected $log = null;
    protected $env = 'dev';
    protected $simulate = false;
    protected $quiet = false;
    protected $skipDefaults = false;
    protected $logdefault = 'debug';

    protected $moreCommands = [
        'gendef' => [
            'help' => 'Generate and show Service Definition for get/post/put/delete operations',
            'options' => [ ['operation', 'Operation Name', 'N', true ]],
        ]
    ];

    public function gendef($options) {

        extract($options);
        /** @var  $operation */
        $operationTitleCase = ucfirst($operation);

        $json = "
        \"get{$operationTitleCase}\": {
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
            \"httpMethod\": \"POST\",
            \"description\": \"Create {$operationTitleCase}\",
            \"uri\": \"{$operation}\",
            \"responseModel\": \"getResponse\",
            \"parameters\": {
                \"product\": {
                    \"sentAs\": \"productsId\",
                    \"location\": \"json\",
                    \"type\": [\"integer\", \"string\"],
                    \"required\": true
                },
                \"public\": {
                    \"sentAs\": \"isPublic\",
                    \"location\": \"json\",
                    \"type\": [\"boolean\", \"string\"]
                }
            }
        },
        \"update{$operationTitleCase}\": {
            \"httpMethod\": \"PUT\",
            \"description\": \"Update {$operationTitleCase}\",
            \"uri\": \"{$operation}/{id}\",
            \"responseModel\": \"getResponse\",
            \"parameters\": {
                \"id\": {
                    \"location\": \"uri\",
                    \"type\": [\"integer\", \"string\"],
                    \"required\": true
                },
                \"product\": {
                    \"sentAs\": \"productsId\",
                    \"location\": \"json\",
                    \"type\": [\"integer\", \"string\"]
                },
                \"public\": {
                    \"sentAs\": \"isPublic\",
                    \"location\": \"json\",
                    \"type\": [\"boolean\", \"string\"]
                }
            }
        },
        \"delete{$operationTitleCase}\": {
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

    public function requestHandler($request)
    {
        if (!$this->quiet) {
            $content = (string)$request->getBody();
            $this->debug("\n\nREQUEST: \n$content\n\n");
        }
        return $request;
    }

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
            }
            else {
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

    protected function getRequestPayloadDefaults($commandName, Options $opt) {

        $payload = parent::getRequestPayloadDefaults($commandName, $opt);

        $commandOptions = $this->commandOptions[$commandName];

        $orgDefaults = $this->config['defaults'] ?? [];
        $selectedDefaults = [$orgDefaults['cc'], $orgDefaults['customer']];
        $options = $this->getPayloadOptions($opt);

        if (empty($payload)) {
            $payload = [];//$data;
        }
        foreach($selectedDefaults as $defaults) {
            foreach ($defaults as $key => $val) {

                if (isset($commandOptions[$key])) {
                    $path = $commandOptions[$key]['path'];
                    $this->arraySetDotted($payload, $path, $val);
                }
            }
        }
        return $payload;

    }

/*    protected function setup(Options $options)
    {
        parent::setup($options);
    }

    protected function registerCommands(Options $options)
    {
        parent::registerCommands($options);
    }

    protected function main(Options $options)
    {
        $result = parent::main($options);
    }

    protected function loadOptions(Options $options) {
        return parent::loadOptions($options);
    }

*/

}
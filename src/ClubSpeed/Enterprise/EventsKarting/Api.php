<?php

namespace SudiptoChoudhury\ClubSpeed\Enterprise\EventsKarting;

use SudiptoChoudhury\Support\Forge\Api\Client as ApiForge;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;

/**
 * Class Api
 *
 *
 * @inheritdoc
 *
 * @package SudiptoChoudhury\ClubSpeed\Enterprise\EventsKarting
 */
class Api extends ApiForge
{

    protected $DEFAULT_API_JSON_PATH = './config/csek-ent.json';
    protected $loggerFile = __DIR__ . '/clubspeed-events-karting-enterprise-api-calls.log';

    protected $DEFAULTS = [
        'api_key' => '',
        'client' => [
            'base_uri' => 'http://f1phx.clubspeedtiming.com/api/index.php/',
        ],
        'settings' => [
            'responseHandler' => null,
            'requestHandler' => null,
        ],

    ];

    /**
     * @param $request Request
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function requestHandler($request)
    {
        $request = $this->injectQuery($request);
        return $request;
    }

    /**
     * @param       $request Request
     * @param array $params
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function injectQuery($request, $params = []) {

        $uri = $request->getUri();
        $query = psr7\parse_query($uri->getQuery());
        $params['key'] = $this->options['api_key'];
        $queryParams = array_merge($params, $query);
        $request  = $request->withUri($uri->withQuery(Psr7\build_query($queryParams)));
        return $request;

    }
//
//    protected function responseHandler($response)
//    {
//        $content = (string)$response->getBody();
//        var_dump($content);
//        return $response;
//    }

}
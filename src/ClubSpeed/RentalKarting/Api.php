<?php

namespace SudiptoChoudhury\ClubSpeed\RentalKarting;

use SudiptoChoudhury\Support\Forge\Api\Client as ApiForge;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;

/**
 * Class Api
 *
 *
 * @inheritdoc
 *
 * @package SudiptoChoudhury\ClubSpeed\RentalKarting
 */
class Api extends ApiForge
{

    protected $DEFAULT_API_JSON_PATH = './config/csrk.json';
    protected $loggerFile = __DIR__ . '/clubspeed-karting-api-calls.log';

    protected $DEFAULTS = [
//        'username' => 'test',
//        'password' => 'test',
        'api_key' => '',
        'client' => [
            'base_uri' => 'http://f1phx.clubspeedtiming.com/api/index.php/',
//            'decode_content' => 'gzip',
//            'verify' => false,
//            'headers' => [
//                'Accept-Encoding' => 'gzip',
//                'Content-Type' => 'application/json',
//            ],
        ],
        'settings' => [
            'responseHandler' => null,
//            'requestHandler' => null,
        ],

    ];
//

    protected function requestHandler($request)
    {
        $request = $this->injectQuery($request);
        var_dump([$request->getUri()->getPath(), $request->getUri()->getQuery()]);
        return $request;
    }

    protected function injectQuery($request, $params = []) {

        $uri = $request->getUri();
        $query = psr7\parse_query($uri->getQuery());
        $params['key'] = $this->options['api_key'];
        $queryParams = array_merge($params, $query);
        $request = new Request('GET', $uri->withQuery(Psr7\build_query($queryParams)));
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
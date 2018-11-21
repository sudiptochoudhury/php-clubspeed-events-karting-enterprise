<?php

namespace SudiptoChoudhury\ClubSpeed\RentalKarting;

use SudiptoChoudhury\Support\Forge\Api\Client as ApiForge;

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
            'query' => ['key' => '{{api_key}}']
        ],
        'settings' => [
            'responseHandler' => null,
            'requestHandler' => null,
        ],

    ];
//
//    protected function requestHandler($request)
//    {
//        $content = (string)$request->getBody();
//        var_dump($request);
//        return $request;
//    }
//
//    protected function responseHandler($response)
//    {
//        $content = (string)$response->getBody();
//        var_dump($content);
//        return $response;
//    }

}
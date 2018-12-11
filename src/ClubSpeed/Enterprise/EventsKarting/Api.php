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
        'log' => false,
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
    protected function injectQuery($request, $params = [])
    {

        $uri = $request->getUri();
        $query = psr7\parse_query($uri->getQuery());
        $params['key'] = $this->options['api_key'];
        $queryParams = array_merge($params, $query);
        $request = $request->withUri($uri->withQuery(Psr7\build_query($queryParams)));
        return $request;

    }


    /**
     * @param array $filters ['where' => [], 'sort' => [], 'limit' => '', 'skip' => '', 'properties' => []]
     *
     * @return array
     */
    public function generateFilters($filters = []) {

        $where = $filters['where'] ?? null;
        $sort = $filters['sort'] ?? null;
        $limit = $filters['limit'] ?? null;
        $skip = $filters['skip'] ?? null;
        $properties = $filters['properties'] ?? null;

        return static::buildQueryEnhancer($filters, $sort, $limit, $skip, $properties);

    }

    /**
     * @param array $filters
     * @param array $sort
     * @param null  $limit
     * @param null  $skip
     * @param array $properties
     *
     * @return array
     */
    public static function buildQueryEnhancer($filters = [], $sort = [], $limit = null, $skip = null, $properties = [])
    {
        return array_merge(
            static::buildFilters($filters),
            static::buildSort($sort),
            static::buildLimit($limit),
            static::buildSkip($skip),
            static::buildPropertiesSelector($properties)
        );
    }

    /**
     * @param array $properties
     *
     * @return array
     */
    public static function buildPropertiesSelector($properties = [])
    {
        if (!empty($properties)) {
            if (is_array($properties)) {
                $select = implode(',', array_values($properties));
            } else {
                $select = $properties;
            }
            return compact('select');
        }
        return [];
    }

    /**
     * @param array $filters
     *                         Examples:
     *
     *                      General syntax:
     *
     *                      [[column, value], [column, value], ...] -
     *                      [[column, operator, value], [column, operator value]]
     *                      [[column, operator, [value, value]], [column, operator, [value, value]]] -for BETWEEN or IN
     *
     *                      Combine with logical operators
     *                      AND         ['&&' => [ [column, value], [column,value] ] ]
     *                      OR          ['||' => [ [column, value], [column,value] ] ]
     *                      NOT         ['!!' => [column, value] ]
     *
     *                      Combine with logical operators
     *
     *                      [ [column, operator, value], ['||' => [column, value], [column,value]] ]]*
     *
     *                      - all can be nested into another
     *
     *                      OPERATORS:
     *                               Simple:
     *                                      <, >, <=, >=, ==, !=,
     *
     *                               Complex:
     *                                      {NULL}      NULL
     *                                      !{NULL}     NO NULL
     *                                      %           LIKE
     *                                      !%          NOT LIKE
     *                                      %%          HAS
     *                                      {IN}        IN (value has to be an indexed array)
     *                                      !{IN},      NOT IN (value has to be an indexed array)
     *                                      ><          BETWEEN (value has to be an array of 2 values), excludes
     *                                                      values from the result
     *                                      >=<=        INCLUSIVE BETWEEN (same as <> but includes values)
     *                                      >=<         BETWEEN INCLUDE START includes only start value
     *                                      ><=         BETWEEN_INCLUDE_END includes only end value
     *
     *                              LOGICAL ROOT OPERATORS
     *                                  &&              AND
     *                                  ||              OR
     *                                  !!              NOT
     *
     *
     *
     * @return array
     */
    public static function buildFilters($filters = [])
    {
        $where = [];
        if (!empty($filters)) {
            $where = json_encode(static::buildFiltersArray($filters));
            return compact('where');
        }
        return $where;
    }

    private static function buildFiltersArray($wheres = [])
    {
        $where = [];

        $rootKeyMap = [
            '&&' => '$and',
            '||' => '$or',
            '!!' => '$not',
        ];
        $operatorsMap = [
            '<' => '$lt',
            '<=' => '$lte',
            '>' => '$gt',
            '>=' => '$gte',
            '==' => '$eq',
            '!=' => '$neq',
            '{NULL}' => '$is',
            '!{NULL}' => '$isnot',
            '{IN}' => '$in',
            '!{IN}' => '$notin',
            '%' => '$like',
            '!%' => '$notlike',
            '%%' => '$has',
            '><' => ['&&' => [['field', '>', 'value'], ['field', '<', 'value']]],
            '>=<' => ['&&' => [['field', '>=', 'value'], ['field', '<', 'value']]],
            '><=' => ['&&' => [['field', '>', 'value'], ['field', '<=', 'value']]],
            '>=<=' => ['&&' => [['field', '>=', 'value'], ['field', '<=', 'value']]],

        ];

        if (!empty($wheres) && is_array($wheres)) {
            $keys = array_keys($wheres);
            if (isset($wheres[0]) && !is_array($wheres[0])) {
                if (!empty($wheres)) {
                    list ($field, $operator, $value) = array_merge($wheres, [null, null]);
                    if (!isset($operatorsMap[$operator])) {
                        $value = $operator;
                        $operator = "==";
                    }
                    $mappedOperator = $operatorsMap[$operator];
                    if (is_array($mappedOperator)) {
                        foreach ($mappedOperator as $rootKey => $params) {
                            $params = static::filtersArrayFieldValueReplacer($params, $field, $value);
                            $where[$rootKeyMap[$rootKey]] = static::buildFiltersArray($params);
                        }
                    } else {
                        $where[] = [$field => [$mappedOperator => $value]];
                    }
                }

            } else {
                foreach ($keys as $key) {
                    $item = $wheres[$key];
                    $rootKey = $rootKeyMap[$key] ?? null;
                    if ($rootKey || is_array($item)) {
                        $filter = static::buildFiltersArray($item);
                        if ($rootKey) {
                            $where[$rootKey] = $filter;
                        } else {
                            $where = array_merge($where, $filter);
                        }
                        unset($wheres[$key]);
                    }
                }
            }
        }

        $keys = array_keys($where);
        $hasNumeric = array_reduce($keys, function ($c, $k) {
            return $c || is_numeric($k);
        }, false);
        $hasKey = array_reduce($keys, function ($c, $k) {
            return $c || !is_numeric($k);
        }, false);
        if ($hasNumeric && $hasKey) {
            $newWhere = [];
            foreach ($where as $key => $item) {
                if (!is_numeric($key)) {
                    $item = [$key => $item];
                }
                $newWhere[] = $item;
            }
            $where = $newWhere;
        }

        return $where;
    }

    private static function filtersArrayFieldValueReplacer($params, $field, $value)
    {
        $filter = [];
        foreach ($params as $index => $data) {
            if (is_array($data)) {
                $filter[$index] = static::filtersArrayFieldValueReplacer($data, $field, $value[$index]);
            } else {
                $filter[$index] = $data;
                if ($data === 'field') {
                    $filter[$index] = $field;
                } elseif ($data === 'value') {
                    $filter[$index] = $value;
                }
            }
        }

        return $filter;
    }

    /**
     * @param array $orders ['columnName' => 'ASC/DESC',  'columnName2' => 'ASC/DESC']
     *
     * @return array
     */
    public static function buildSort($orders = [])
    {
        $sort = [];

        if (!empty($orders)) {
            if (!is_array($orders)) {
                $orders = [$orders];
            }
            if (isset($orders[0])) {
                if (is_array($orders[0])) {
                    foreach ($orders as $items) {
                        $order = static::buildSort($items);
                        $sort = array_merge($sort, [$order['order']]);
                    }
                } else {
                    $sort[] = implode(' ', $orders);
                }
            } else {
                foreach ($orders as $key => $order) {
                    $sort[] = implode(' ', [$key, strtoupper($order ? : 'ASC')]);
                }
            }
            return ['order' => implode(',', $sort)];
        }
        return [];
    }

    /**
     * @param null $limit
     *
     * @return array
     */
    public static function buildLimit($limit = null)
    {
        if (!is_null($limit)) {
            return compact('limit');
        }
        return [];
    }

    /**
     * @param null $skip
     *
     * @return array
     */
    public static function buildSkip($skip = null)
    {
        if (!is_null($skip)) {
            return compact('skip');
        }
        return [];
    }
}
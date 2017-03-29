<?php

/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

namespace aliyun\live;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;
use aliyun\guzzle\subscriber\Rpc;

/**
 * Class Client
 * @package aliyun\live
 */
class Client
{
    /**
     * @var string
     */
    public $accessKeyId;

    /**
     * @var string
     */
    public $accessSecret;

    /**
     * @var string API版本
     */
    public $version = '2016-11-01';

    /**
     * @var string 网关地址
     */
    public $baseUri = 'http://live.aliyuncs.com/';

    /**
     * @var HttpClient
     */
    private $_httpClient;

    /**
     * Request constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        foreach ($config as $name => $value) {
            $this->{$name} = $value;
        }
    }

    /**
     * 获取Http Client
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (!is_object($this->_httpClient)) {
            $stack = HandlerStack::create();
            $middleware = new Rpc([
                'accessKeyId' => $this->accessKeyId,
                'accessSecret' => $this->accessSecret,
                'Version' => $this->version
            ]);
            $stack->push($middleware);

            $this->_httpClient = new HttpClient([
                'base_uri' => $this->baseUri,
                'handler' => $stack,
                'verify' => false,
                'http_errors' => false,
                'connect_timeout' => 3,
                'read_timeout' => 10,
                'debug' => false,
            ]);
        }
        return $this->_httpClient;
    }

    /**
     * @param array $params
     * @return string
     */
    public function createRequest(array $params)
    {
        return $this->getHttpClient()->get('/', ['query' => $params]);
    }
}
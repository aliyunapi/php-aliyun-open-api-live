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

use aliyun\live\auth\ShaHmac1Signer;

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
     * @var \aliyun\core\auth\SignerInterface 签名算法实例
     */
    public $signer;



    /**
     * @var \GuzzleHttp\Client
     */
    public $_httpClient;

    /**
     * Request constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        foreach ($config as $name => $value) {
            $this->{$name} = $value;
        }
        $this->init();
    }

    public function init(){
        $this->signer = new ShaHmac1Signer();
    }

    /**
     * 获取Http Client
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        if (!is_object($this->_httpClient)) {
            $this->_httpClient = new \GuzzleHttp\Client([
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
        $params['Format'] = 'JSON';
        $params['Version'] = '2016-11-01';
        $params['AccessKeyId'] = $this->accessKeyId;
        $params['SignatureMethod'] = $this->signer->getSignatureMethod();
        $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
        $params['SignatureVersion'] = $this->signer->getSignatureVersion();
        $params['SignatureNonce'] = uniqid();
        //签名
        $params['Signature'] = $this->computeSignature($params);
        $requestUrl = $this->composeUrl('http://live.aliyuncs.com/', $params);
        $response = $this->sendRequest('GET', $requestUrl);
        return $response->getBody()->getContents();
    }

    /**
     * Sends HTTP request.
     * @param string $method request type.
     * @param string $url request URL.
     * @param array $options request params.
     * @return object response.
     */
    public function sendRequest($method, $url, array $options = [])
    {
        $response = $request = $this->getHttpClient()->request($method, $url, $options);
        return $response;
    }

    /**
     * 合并基础URL和参数
     * @param string $url base URL.
     * @param array $params GET params.
     * @return string composed URL.
     */
    protected function composeUrl($url, array $params = [])
    {
        if (strpos($url, '?') === false) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        $url .= http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        return $url;
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function computeSignature($parameters)
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $stringToSign = 'GET&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
        $signature = $this->signer->signString($stringToSign, $this->accessSecret . "&");

        return $signature;
    }

    protected function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
}
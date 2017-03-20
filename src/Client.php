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

class Client
{
    public $secretId;
    public $secretKey;

    public $version = '2016-11-01';

    /**
     * @var string 时间格式
     */
    public $dateTimeFormat = 'Y-m-d\TH:i:s\Z';

    /**
     * Client constructor.
     * @param string $secretId
     * @param string $secretKey
     */
    public function __construct($secretId, $secretKey)
    {
        $this->secretId = $secretId;
        $this->secretKey = $secretKey;
    }

    public function request(array $params)
    {
        $params['Format'] = 'JSON';
        $params['Version'] = $this->version;
        $params['AccessKeyId'] = $this->secretId;
        $params['SignatureMethod'] = 'HMAC-SHA1';
        $params['Timestamp'] = gmdate($this->dateTimeFormat);
        $params['SignatureVersion'] = '1.0';
        $params['SignatureNonce'] = uniqid();
        $plainText = $this->makeSignPlainText($params);
        //签名
        $plainText = 'GET&%2F&' . $this->percentencode(substr($plainText, 1));
        $params['Signature'] = base64_encode(hash_hmac('sha1', $plainText, $this->secretKey . "&", true));
        $client = new \GuzzleHttp\Client([
            'verify' => false,
            'http_errors' => false,
            'connect_timeout' => 3,
            'read_timeout' => 10,
            'debug' => true,
        ]);
        $requestUrl = 'http://live.aliyuncs.com/?';
        foreach ($params as $apiParamKey => $apiParamValue) {
            $requestUrl .= "$apiParamKey=" . urlencode($apiParamValue) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);
        $request = new \GuzzleHttp\Psr7\Request('GET', $requestUrl, [
            'client' => 'php/1.0.0',
        ]);
        $response = $client->send($request);
        return $response->getBody()->getContents();
    }

    /**
     * @param $parameters
     * @return mixed
     */
    private function makeSignPlainText($parameters)
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        return $canonicalizedQueryString;
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
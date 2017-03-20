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

use aliyun\core\auth\ShaHmac1Signer;

class Client extends \aliyun\core\Client
{
    /**
     * 初始化
     */
    public function init()
    {
        parent::init();

    }

    /**
     * @param array $params
     * @return string
     */
    public function createRequest()
    {
        return new Request([
            'accessKeyId' => $this->accessKeyId,
            'accessSecret' => $this->accessSecret,
        ]);
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


}
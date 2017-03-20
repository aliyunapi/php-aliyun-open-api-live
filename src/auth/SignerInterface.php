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
namespace aliyun\live\auth;

/**
 * 签名接口
 * @package aliyun\core\auth
 */
interface SignerInterface
{
    /**
     * 获取签名方法
     * @return string
     */
    public function getSignatureMethod();

    /**
     * 获取签名版本
     * @return string
     */
    public function getSignatureVersion();

    /**
     * 对字符串进行签名
     * @param string $source
     * @param string $accessSecret
     * @return string
     */
    public function signString($source, $accessSecret);
}
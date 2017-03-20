<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace aliyun\live;

use aliyun\core\Base;
use aliyun\core\auth\ShaHmac1Signer;

class Request extends Base
{
    public $accessKeyId;

    public $accessSecret;



    /**
     * @var \GuzzleHttp\Client
     */
    public $_httpClient;

    public function init(){
        parent::init();

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
     * 通过__call转发请求
     * @param  string $name 方法名
     * @param  array $arguments 参数
     * @return
     */
    public function __call($name, $arguments)
    {
        $action = ucfirst($name);
        $params = [];
        if (is_array($arguments) && !empty($arguments)) {
            $params = (array)$arguments[0];
        }
        $params['Action'] = $action;
        return $this->_dispatchRequest($params);
    }

    /**
     * 发起接口请求
     * @param array $params 接口参数
     * @return
     */
    protected function _dispatchRequest($params)
    {
        $params['Format'] = 'JSON';
        $params['Version'] = '2016-11-01';
        $params['AccessKeyId'] = $this->accessKeyId;
        $params['SignatureMethod'] = 'HMAC-SHA1';
        $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
        $params['SignatureVersion'] = '1.0';
        $params['SignatureNonce'] = uniqid();
        //签名
        $params['Signature'] = $this->computeSignature($params);
        $requestUrl = $this->composeUrl('http://live.aliyuncs.com/', $params);

        $response = $request = $this->getHttpClient()->request('GET', $requestUrl);
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
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->accessSecret . "&", true));

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
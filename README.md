# php-aliyun-open-api-live

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist aliyunapi/php-aliyun-open-api-live
```

or add

```
"aliyunapi/php-aliyun-open-api-live": "~1.0"
```

to the require section of your composer.json.

使用方式
------------
```
$client = new \aliyun\live\Client('123456', '123456');
$package = [
    'Action' => 'DescribeLiveStreamsPublishList',
    'DomainName' => 'live.cctv.com',
    'StartTime' => gmdate('Y-m-d\TH:i:s\Z', strtotime('2017-03-15')),
    'EndTime' => gmdate('Y-m-d\TH:i:s\Z', strtotime('2017-04-01')),
];
$response = $client->createRequest($package);
print_r($response);
exit;
```
<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace aliyun\test;


class ClientTest extends TestCase
{

    public function testExpirationTime()
    {
        $params = [
            'action' => 'publish',
            'ip' => '127.0.0.1',
            'id' => '123456',
            'app' => 'live.cctv.com',
            'appname' => 'cctv5',
            'time' => 1488966279,
            'usrargs' => 'vhost%3Dlive.opencoding.tv%26auth_key%3D1489569710-0-0-499df36a0e5fd6d2bdbd72b877b7896a',
            'node' => 'eu6',
        ];

        $this->client->setExpirationTime(1488966279);
        $this->assertEquals(1488966279, $this->client->getExpirationTime());


    }

    public function testSign()
    {
        $this->client->setExpirationTime(1488966279);
        $sign = $this->client->getSign('123456');
        $this->assertTrue($this->client->checkSign('123456',$sign));

        $sign = $this->client->getSign('123456');
        $this->assertFalse($this->client->checkSign('1234567',$sign));

    }
}

<?php
require "Api.php";
require "PhpLoad.php";
require "request/HttpRequest.php";
class Client
{
    public function test()
    {
        $api = new Api();
        $api->uri = 'www.vipshop.com';
        $api->requestData = "GET / HTTP/1.0\r\nHost: www.vipshop.com\r\nAccept: */*\r\n\r\n";
        $api->connectTimeOut = 1000;
        $api->readTimeOut = 5000;
        $api->writeTimeOut = 5000;
        $api->maxReqNum = 1000;
        $api->pressValue = 2;
        $api->request = new HttpRequest();
        $phpLoad = new PhpLoad($api);
        $phpLoad->run();
    }
}

$client = new Client();
$client->test();

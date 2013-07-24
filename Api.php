<?php
class Api
{
    //tcp://www.example.com:80
    public $uri;

    //GET / HTTP/1.0\r\nHost: www.example.com\r\nAccept: */*\r\n\r\n
    public $requestData;

    public $connectTimeOut;
    public $readTimeOut;
    public $writeTimeOut;

    public $request;

    public $maxReqNum;

    public function isExpectedResponse($data)
    {
        if ($data) {
            return true;
        }
        return false;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getMaxReqNum()
    {
        return $this->maxReqNum;
    }

}

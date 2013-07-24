<?php
require "Request.php";

class HttpRequest extends Request
{

    public $response;

    public function __construct()
    {
    }

    
    public function  sendRequest($api)
    {
        // socket_write
        $fp = stream_socket_client('tcp://'.$api->uri.':80', $errno, $errstr, $api->readTimeOut);
        if (!$fp) {
            return;
        }
        fwrite($fp, $api->requestData);
        while (!feof($fp)) {
            $this->response .= fgets($fp, 1024);
        }
        fclose($fp);
    }

    public function getResponse()
    {
        return $this->response;
    }

}

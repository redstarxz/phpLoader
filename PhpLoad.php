<?php
class PhpLoad
{
    public $startTime = 0;

    public $count = 0;

    private $_apiObj;

    private $request;

    public function __construct($apiObj)
    {
        $this->startTime = microtime(true);
        $this->_apiObj = $apiObj;
    }

    public function run()
    {
        $this->pressValue = $this->_apiObj->pressValue;;
        $this->request = $this->_apiObj->getRequest();
        $maxReqNum = $this->_apiObj->getMaxReqNum();
        while ($this->count < $maxReqNum) {
            $this->request->sendRequest($this->_apiObj);
            $this->totalResult($this->request->getResponse());
            $this->count++;
            if ($this->count % $this->pressValue === 0) {
                echo "it is {$this->count}th request\n";
                $end = microtime(true);
                $this->waitToNextSec($this->startTime, $end);
                $this->startTime = microtime(true);
            }
        }
    }

    private function waitToNextSec($start, $end)
    {
        if ($end - $start > 1) {
            echo "{$this->pressValue} requests finished more than 1 s\n";
        } else {
            $used = $end - $start;
            echo "$this->pressValue requests finished successfully in $used seconds\n";
            usleep(($start + 1 - $end) * 1000000);
        }
    }

    public function totalResult()
    {
       echo $this->request->getResponse(); 
    }
}

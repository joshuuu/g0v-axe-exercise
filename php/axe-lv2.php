<?php

class Level2
{
    private $curlHandle;
    private $domHandle;
    private $resultList;

    public function __construct()
    {
        $this->curlHandle = curl_init();
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, 1);
        $this->domHandle = new DOMDocument();
        $this->resultList = [];
    }

    public function parse($url)
    {
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);
        $html = curl_exec($this->curlHandle);
        @$this->domHandle->loadHtml($html);

        $trList = $this->domHandle->getElementsByTagName('tr');
        if ($trList->length <= 1) {
            return false;
        }
        for ($i=1; $i<$trList->length; $i++) {
            $tr = $trList->item($i);
            $tdList = $tr->getElementsByTagName('td');
            $townObj = new StdClass;
            $townObj->town = $tdList->item(0)->nodeValue;
            $townObj->village = $tdList->item(1)->nodeValue;
            $townObj->name = $tdList->item(2)->nodeValue;
            array_push($this->resultList, $townObj);
        }
    }

    public function getJsonResult()
    {
        return json_encode($this->resultList, JSON_UNESCAPED_UNICODE);
    }

    public function __destruct()
    {
        curl_close($this->curlHandle);
    }
}

function main()
{
    define("BASE_URL", "http://axe-level-1.herokuapp.com/lv2/");
    $curlHandle = curl_init(BASE_URL);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);

    $html = curl_exec($curlHandle);
    $domHandle = new DOMDocument();
    @$domHandle->loadHTML($html);
    $lv2Obj = new Level2();
    foreach ($domHandle->getElementsByTagName('a') as $aNode) {
        $pageIndex = $aNode->getAttribute('href');
        $lv2Obj->parse(BASE_URL.$pageIndex);
    }
    curl_close($curlHandle);

    echo $lv2Obj->getJsonResult();
    return 0;
}

exit(main());

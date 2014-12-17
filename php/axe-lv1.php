<?php

function level1()
{
    $ret = 0;
    $url = 'http://axe-level-1.herokuapp.com';
    $curlHandle = curl_init($url);
    $dom = new DOMDocument();
    $titleList = [];
    $personList = [];

    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    if (false === ($html = curl_exec($curlHandle))) {
        $ret = -3;
        goto end;
    }

    @$dom->loadHTML($html);
    foreach ($dom->getElementsByTagName('tr') as $tr) {
        if (!$tr->hasChildNodes()) {
            break;
        }
        if ('姓名' === $tr->firstChild->nodeValue) {
            $gradeObj = new StdClass;
            foreach ($tr->getElementsByTagName('td') as $td) {
                array_push($titleList, $td->nodeValue);
            }
        } else {
            $person = new StdClass;
            $tdNodes = $tr->getElementsByTagName('td');
            $person->name = $tdNodes[0]->nodeValue;
            $person->grades = new StdClass;
            for ($i = 1; $i<$tdNodes->length; $i++) {
                $person->grades->{$titleList[$i]} = intval($tdNodes[$i]->nodeValue);
            }
            array_push($personList, $person);
        }
    }

    echo json_encode($personList, JSON_UNESCAPED_UNICODE);

end:
    curl_close($curlHandle);
    return $ret;
}

exit(level1());


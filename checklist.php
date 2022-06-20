<?php

require __DIR__ . '/vendor/autoload.php';

use App\Stream;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);



$minos = new Stream($dotenv, 'Minos');
$eaques = new Stream($dotenv, 'Eaques');
$rhadamanthe = new Stream($dotenv, 'Rhadamanthe');

$minos->createConnexions();
$rhadamanthe->createConnexions();
$eaques->createConnexions();

//$expire = $minos->whoisDomain($domainTocheck);

//$expireDate = $minos->get_string_between($expire,"Expiry Date:", "created:");
//$expireDate = trim($expireDate);



$strJsonFileContents = file_get_contents("urlList.txt");
$array = explode(PHP_EOL, $strJsonFileContents);
$availableContent = array();
$notAvailableContent = array();
foreach ($array as $domain){
    $availability = $minos->checkdomain($domain);

    if($availability == '1'){
        //if(!empty($availableContent)){
        //    $availableContent = file_get_contents("./NDD/available.json");
        //    array_push(json_encode($availableContent) , $domain);
        //} else {
        //    $availableContent[] = $domain;
        //}

        $availableContent[] = $domain . PHP_EOL;

        file_put_contents('./NDD/available.json', $availableContent);
    } else {



    $notAvailableContent[] = $domain . PHP_EOL;

        file_put_contents('./NDD/notAvailable.json',$notAvailableContent);
    }
}


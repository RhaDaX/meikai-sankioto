<?php 

require __DIR__ . '/vendor/autoload.php';

use App\Stream;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);



$minos = new Stream($dotenv, 'Minos');
//$eaques = new Stream($dotenv, 'Eaques');

//$minos->createConnexions();
//sleep(1);
//$eaques->createConnexions();
// $minosAvail = $minos->checkdomain('linkweb.fr');
// if($minosAvail === "0"){
//     $whois = shell_exec("whois linkweb.fr");
//     echo '<pre>';
//     print_r($whois);

// }
//$eaques->checkdomain('afnic.fr');
$domainTocheck = 'opca3plus.fr';
$expire = $minos->whoisDomain($domainTocheck);

$expireDate = $minos->get_string_between($expire,"Expiry Date:", "created:");
$expireDate = trim($expireDate);
//echo date("d/m/Y", strtotime($expireDate));
echo '<pre>';
//echo date("h:i:s", strtotime($expireDate));

$domain = array();

/*
 * opca3plus.fr
 * linkweb.Fr
 */

/*
 * Vielle Version
$strJsonFileContents = file_get_contents("domain.json");
// Convert to array
if($strJsonFileContents == NULL){

    $domain[0]['name'] = $domainTocheck;
    $domain[0]['expiryDate'] = date("d/m/Y", strtotime($expireDate));
    $domain[0]['expiryTime'] = date("H:i:s", strtotime($expireDate));
    $minute = date('i', strtotime($expireDate));
    if($minute > '32') {
        $domain[0]['launchTime'] = date("H:i", strtotime("+1 hour", strtotime($expireDate)));
    } else {
        $domain[0]['launchTime'] = date("H:i", strtotime($expireDate));
    }
    $jsonData = json_encode($domain);
    file_put_contents('domain.json', $jsonData);
} else {

    $domain['name'] = $domainTocheck;
    $domain['expiryDate'] = date("d/m/Y", strtotime($expireDate));
    $domain['expiryTime'] = date("H:i:s", strtotime($expireDate));
    $minute = date('i', strtotime($expireDate));
    if($minute > '32') {
        $domain['launchTime'] = date("H", strtotime("+1 hour", strtotime($expireDate))) . ':22';
    } else {
        $domain['launchTime'] = date("H:i", strtotime($expireDate));
    }
    $array = json_decode($strJsonFileContents, true);
    array_push($array, $domain);
    var_dump($array);
    $jsonData = json_encode($array);
    file_put_contents('domain.json', $jsonData);
} */

/* Version fonctionnelle

$strJsonFileContents = file_get_contents("domain.json");
$array = json_decode($strJsonFileContents, true);
$count = ($array) ? count($array) : 0;
$i = $count + 1;
// Convert to array
if($count === 0){

    $domain[0][$domainTocheck] = array();
    $domain[0][$domainTocheck]['expiryDate'] = date("d/m/Y", strtotime($expireDate));
    $domain[0][$domainTocheck]['expiryTime'] = date("H:i:s", strtotime($expireDate));
    $minute = date('i', strtotime($expireDate));
    if($minute > '32') {
        $domain[0][$domainTocheck]['launchTime'] = date("H:i", strtotime("+1 hour", strtotime($expireDate)));
    } else {
        $domain[0][$domainTocheck]['launchTime'] = date("H:i", strtotime($expireDate));
    }
    $jsonData = json_encode($domain);
    file_put_contents('domain.json', $jsonData);
} else {
    $array = json_decode($strJsonFileContents, true);
    var_dump($array);
    $domain[$i][$domainTocheck] = array();
    $domain[$i][$domainTocheck]['expiryDate'] = date("d/m/Y", strtotime($expireDate));
    $domain[$i][$domainTocheck]['expiryTime'] = date("H:i:s", strtotime($expireDate));
    $minute = date('i', strtotime($expireDate));
    if($minute > '32') {
        $domain[$i][$domainTocheck]['launchTime'] = date("H", strtotime("+1 hour", strtotime($expireDate))) . ':22';
    } else {
        $domain[$i][$domainTocheck]['launchTime'] = date("H:i", strtotime($expireDate));
    }

    array_push($array, $domain[$i]);
   // var_dump($array);
    $jsonData = json_encode($array);
    file_put_contents('domain.json', $jsonData);
}*/

//$minos->append_cronjob(date("s", strtotime($expireDate)) .' '. date("i", strtotime($expireDate))  .' '. date("h", strtotime($expireDate))  .' * * curl -s index.php');



$minos->checkIfItsTime();
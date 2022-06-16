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
//sleep(1);
//$eaques->createConnexions();
// $minosAvail = $minos->checkdomain('linkweb.fr');
// if($minosAvail === "0"){
//     $whois = shell_exec("whois linkweb.fr");
//     echo '<pre>';
//     print_r($whois);

// }
//$eaques->checkdomain('afnic.fr');
$domainTocheck = 'chr-reunion.fr';
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


/* Version fonctionnelle
*/
/*$strJsonFileContents = file_get_contents("domain.json");
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
        $domain[$i][$domainTocheck]['launchTime'] = date("H", strtotime($expireDate)). ':22';
    }

    array_push($array, $domain[$i]);
   // var_dump($array);
    $jsonData = json_encode($array);
    file_put_contents('domain.json', $jsonData);
}
*/
//$minos->append_cronjob(date("s", strtotime($expireDate)) .' '. date("i", strtotime($expireDate))  .' '. date("h", strtotime($expireDate))  .' * * curl -s index.php');



$scanTime = $minos->checkIfItsTime();

var_dump($scanTime);
if($scanTime != false){
    $today =  \DateTime::createFromFormat('d/m/Y H:i', date("d/m/Y H:i"));
    $today->setTimezone(new \DateTimeZone('Europe/Paris'));
    $altar = 'rhada';
    $exit = false;
    /*while($today <= $scanTime && $exit === false){

        if($altar === 'rhada'){
            usleep(210000);
            $rhadaResult = $rhadamanthe->checkdomain($domainTocheck);
            echo  PHP_EOL ;
            echo 'Rhadamanthe :' . $rhadaResult;
            var_dump(\DateTime::createFromFormat('U.u', microtime(true)));
            $altar = 'eaques';
            $exit = $rhadaResult;
        } elseif($altar === 'eaques') {
            usleep(210000);
            $eaquesResult = $eaques->checkdomain($domainTocheck);
            echo  PHP_EOL ;
            echo 'Eaques :' . $eaquesResult;
            var_dump( \DateTime::createFromFormat('U.u', microtime(true)));
            $altar = 'minos';
        } else {
            usleep(210000);
            $minosResult = $minos->checkdomain($domainTocheck);
            echo  PHP_EOL ;
            echo 'Minos :' . $minosResult;
            var_dump( \DateTime::createFromFormat('U.u', microtime(true)));
            $altar = 'rhada';
        }
    }*/
    /**
     * Version de test Simulation de libération d'un domaine
     */
    while($today <= $scanTime && $exit === false){

        if($altar === 'rhada'){
            usleep(640000);
            $rhadaResult = $rhadamanthe->simulateCheckDomain();
            echo  PHP_EOL ;
            echo 'Rhadamanthe :' . $rhadaResult;
            var_dump(\DateTime::createFromFormat('U.u', microtime(true)));
            $altar = 'eaques';
            $exit = $rhadaResult;
            if($rhadaResult === true){
                echo 'Eaques snipe le domaine';
                $eaques->snipeDomain();
            }
        } elseif($altar === 'eaques') {
            usleep(640000);
            $eaquesResult = $eaques->simulateCheckDomain();
            echo 'Eaques :' . $eaquesResult;
            var_dump( \DateTime::createFromFormat('U.u', microtime(true)));
            $altar = 'minos';
            $exit = $rhadaResult;
        } else {
            usleep(640000);
            $minosResult = $minos->simulateCheckDomain();
            echo  PHP_EOL ;
            echo 'Minos :' . $minosResult;
            var_dump( \DateTime::createFromFormat('U.u', microtime(true)));
            $altar = 'rhada';
            $exit = $rhadaResult;
        }
    }

}

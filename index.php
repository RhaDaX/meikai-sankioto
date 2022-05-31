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
$domainTocheck = 'linkweb.fr';
$expire = $minos->whoisDomain($domainTocheck);

$expireDate = $minos->get_string_between($expire,"Expiry Date:", "created:");
$expireDate = trim($expireDate);
echo date("d/m/Y", strtotime($expireDate));
echo '<pre>';
echo date("h:i:s", strtotime($expireDate));

$domain = array();
$domain['name'] = $domainTocheck;
$domain['expiryDate'] = date("d/m/Y", strtotime($expireDate));
$domain['expiryTime'] = date("h:i:s", strtotime($expireDate));

$fp = fopen('domain.json', 'w');
fwrite($fp, json_encode($domain));
fclose($fp);

$minos->append_cronjob(date("s", strtotime($expireDate)) .' '. date("i", strtotime($expireDate))  .' '. date("h", strtotime($expireDate))  .' * * curl -s index.php');
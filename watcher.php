<?php

require __DIR__ . '/vendor/autoload.php';

use App\Stream;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);


$minos = new Stream($dotenv, 'Minos');
$eaques = new Stream($dotenv, 'Eaques');
$rhadamanthe = new Stream($dotenv, 'Rhadamanthe');


$scanTime = $minos->checkIfItsTime();


if($scanTime != false){

    $minos->createConnexions();
    $rhadamanthe->createConnexions();
    $eaques->createConnexions();


    $today =  \DateTime::createFromFormat('d/m/Y H:i', date("d/m/Y H:i"));
    $today->setTimezone(new \DateTimeZone('Europe/Paris'));
    $altar = 'rhada';
    $exit = false;

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
            $exit = $eaquesResult;
        } else {
            usleep(640000);
            $minosResult = $minos->simulateCheckDomain();
            echo  PHP_EOL ;
            echo 'Minos :' . $minosResult;
            var_dump( \DateTime::createFromFormat('U.u', microtime(true)));
            $altar = 'rhada';
            $exit = $minosResu;
        }
    }
    file_put_contents('cron.txt', 'Lightening Plasma');

}





?>
<?php 

require __DIR__ . '/vendor/autoload.php';

use App\Stream;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

$stream = new Stream();

//secho 'ça passe';
$stream->createConnexion($dotenv);
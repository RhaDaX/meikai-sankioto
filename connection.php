<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

$dotenv->load();

$host = $_ENV['HOST'];
$port = $_ENV['PORT'];
$cert = $_ENV['CERT'];
$login = $_ENV['LOGIN'];
$password = $_ENV['PASSWORD'];
$context = stream_context_create(array('ssl' => array('local_cert' => $cert,"verify_peer" => true,"verify_peer_name"=>true)));
function fullread($fp, $count) {
    $buffer = "";
    while ($count > 0) {
        $data = fread($fp, $count);
        if ($data === FALSE) {
            die("ERROR: fread failed");
        }
        $count -= strlen($data);
        $buffer .= $data;
    }
    return $buffer;
}



function receive($fp) {
    $data = fullread($fp, 4);
    $count = unpack('N', $data);
    $count = $count[1];
    $buffer = fullread($fp, $count - 4);
    return $buffer;
}
$fp = stream_socket_client('ssl://'.$host.':'.$port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT,
    $context);
if (! $fp) {
    exit("ERROR: $errno - $errstr<br />\n");
}
$frame = receive($fp);
printf("RECEIVED:\n%s\n", $frame);
$xlogin = htmlspecialchars($login, ENT_XML1);
$xpw = htmlspecialchars($password, ENT_XML1);
$buffer = "<?xml version='1.0' encoding='UTF-8'?><epp xmlns='urn:ietf:params:xml:ns:epp-1.0' >
    <command>
        <login><clID>$xlogin</clID><pw>$xpw</pw><options><version>1.0</version><lang>en</lang></options><svcs><objURI>urn:ietf:params:xml:ns:contact-1.0</objURI><objURI>urn:ietf:params:xml:ns:domain-1.0</objURI><objURI>urn:ietf:params:xml:ns:host-1.0</objURI><svcExtension><extURI>urn:ietf:params:xml:ns:rgp-1.0</extURI><extURI>http://www.afnic.fr/xml/epp/frnic-1.4</extURI></svcExtension></svcs></login>
    </command>

        

    </epp>";
$buffer2 = '<?xml version="1.0"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
 <command>
 <check>
<domain:check xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
<domain:name>rhfsjgc.fr</domain:name>
</domain:check>
 </check>
 <clTRID>rMVmEWKgUj2LL4UTZYj8K+ig</clTRID>
 </command>
</epp>';
$buffer3 = '<?xml version="1.0"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
 <command>
 <create>
<contact:create xmlns:contact="urn:ietf:params:xml:ns:contact-1.0">
<contact:id>XXX</contact:id>
<contact:postalInfo type="loc">
<contact:name>Petit</contact:name>
<contact:addr>
<contact:street>1 rue Stephenson</contact:street>
<contact:city>Montigny le Bretonneux</contact:city>
<contact:pc>78180</contact:pc>
<contact:cc>FR</contact:cc>
</contact:addr>
</contact:postalInfo>
<contact:email>contact@afnic.fr</contact:email>
<contact:authInfo>
<contact:pw>nZuGUTUVCjwFYvcMFXf+wOrXDFi9C4mQ</contact:pw>
</contact:authInfo>
</contact:create>
 </create>
 <extension>
<frnic:ext xmlns:frnic="http://www.afnic.fr/xml/epp/frnic-1.4">
<frnic:create>
<frnic:contact>
<frnic:firstName>Marie</frnic:firstName>
</frnic:contact>
</frnic:create>
</frnic:ext>
 </extension>
 <clTRID>zEvosWCSHqbCNr7OlMaZ11F3</clTRID>
 </command>
</epp>';
$buffer4 = '<?xml version="1.0"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
 <command>
 <create>
<domain:create xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
<domain:name>testlinkweb.fr</domain:name>
<domain:period unit="y">1</domain:period>
<domain:registrant>MP61713</domain:registrant>
<domain:contact type="admin">MP61713</domain:contact>
<domain:contact type="tech">NC32276</domain:contact>
<domain:authInfo>
<domain:pw>iv2252UtF8N/kF7atGH3iCaf</domain:pw>
</domain:authInfo>
</domain:create>
 </create>
 <clTRID>TMcF3I+zGO1VS5gO7pJWDkVn</clTRID>
 </command>
</epp>';
fwrite($fp, pack('N', 4 + strlen($buffer)));
fwrite($fp, $buffer);
printf("SENT:\n%s\n", $buffer);
$frame = receive($fp);
printf("RECEIVED first:\n%s\n", $frame);

//$whois = shell_exec("whois linkweb.fr");
//echo '<pre>';
//print_r($whois);

echo 'Finish';
// fwrite($fp, pack('N', 4 + strlen($buffer4)));
// fwrite($fp, $buffer4);
// $frame = receive($fp);
// printf("RECEIVED second:\n%s\n", $frame);

fclose($fp);
//NC32276-FRNIC contact technique de test

//MP61713-FRNIC contact de test 
?>


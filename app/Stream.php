<?php 
namespace App;
//require __DIR__ . '/vendor/autoload.php';
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);



class Stream
{
    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    protected $path;

    protected $host;

    protected $port;
    
    protected $cert;

    protected $login;

    protected $password;

    public function __construct()
    
    {

    }

    public function createConnexion($dotenv) :void
    {
         $dotenv->load();

        $host = $_ENV['HOST'];
        $port = $_ENV['PORT'];
        $cert = $_ENV['CERT'];
        $login = $_ENV['LOGIN'];
        $password = $_ENV['PASSWORD'];

        $xlogin = htmlspecialchars($login, ENT_XML1);

        $xpw = htmlspecialchars($password, ENT_XML1);


        $context = stream_context_create(array('ssl' => array('local_cert' => $cert,"verify_peer" => true,"verify_peer_name"=>true)));
        $fp = stream_socket_client('ssl://'.$host.':'.$port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        if (! $fp) {
            exit("ERROR: $errno - $errstr<br />\n");
        }
        $frame = $this->receive($fp);
        echo 'Crimson Thorn !!!';
        $buffer = "<?xml version='1.0' encoding='UTF-8'?><epp xmlns='urn:ietf:params:xml:ns:epp-1.0' >
    <command>
        <login><clID>$xlogin</clID><pw>$xpw</pw><options><version>1.0</version><lang>en</lang></options><svcs><objURI>urn:ietf:params:xml:ns:contact-1.0</objURI><objURI>urn:ietf:params:xml:ns:domain-1.0</objURI><objURI>urn:ietf:params:xml:ns:host-1.0</objURI><svcExtension><extURI>urn:ietf:params:xml:ns:rgp-1.0</extURI><extURI>http://www.afnic.fr/xml/epp/frnic-1.4</extURI></svcExtension></svcs></login>
    </command>
    </epp>";
        while(true){
        fwrite($fp, pack('N', 4 + strlen($buffer)));
        fwrite($fp, $buffer);
        $frame = $this->receive($fp);
        }

    }

    private function fullread($fp, $count) {
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

    private function receive($fp) {
        $data = $this->fullread($fp, 4);
        $count = unpack('N', $data);
        $count = $count[1];
        $buffer = $this->fullread($fp, $count - 4);
        return $buffer;
    }
}

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

    protected $fp;

    protected $name;

    public function __construct($dotenv, $name)
    {
        $dotenv->load();
        $this->host = $_ENV['HOST'];
        $this->port = $_ENV['PORT'];
        $this->cert = $_ENV['CERT'];
        $this->login = $_ENV['LOGIN'];
        $this->password = $_ENV['PASSWORD'];
        $this->name = $name;
    }

    public function createConnexions()
    {

        $context = stream_context_create(array('ssl' => array('local_cert' => $this->cert,"verify_peer" => true,"verify_peer_name"=>true)));

        $this->fp = stream_socket_client('ssl://'.$this->host.':'.$this->port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);

        $xlogin = htmlspecialchars($this->login, ENT_XML1);
        $xpw = htmlspecialchars($this->password, ENT_XML1);
        //$fp = stream_socket_client('ssl://'.$host.':'.$port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        if (! $this->fp) {
            exit("ERROR: $errno - $errstr<br />\n");
        }
        $frame = $this->receive($this->fp);
        $buffer = "<?xml version='1.0' encoding='UTF-8'?><epp xmlns='urn:ietf:params:xml:ns:epp-1.0' >
            <command>
                <login><clID>$xlogin</clID><pw>$xpw</pw><options><version>1.0</version><lang>en</lang></options><svcs><objURI>urn:ietf:params:xml:ns:contact-1.0</objURI><objURI>urn:ietf:params:xml:ns:domain-1.0</objURI><objURI>urn:ietf:params:xml:ns:host-1.0</objURI><svcExtension><extURI>urn:ietf:params:xml:ns:rgp-1.0</extURI><extURI>http://www.afnic.fr/xml/epp/frnic-1.4</extURI></svcExtension></svcs></login>
            </command>
            </epp>";
        fwrite($this->fp, pack('N', 4 + strlen($buffer)));
        fwrite($this->fp, $buffer);
        $frame = $this->receive($this->fp);
        $connexion['name'] = $this->name;
        $connexion['fp'] = $this->fp;
        //$connexion = $this->keepConnectionAlive($this->fp, $this->name);
        return $connexion;
    }


    private function keepConnectionAlive($fp, $name){
        echo $name .' connecté !<br>';
        $connexion['name'] = $this->name;
        $connexion['fp'] = $fp;

        return $connexion;
        // while(true){

        //     $frame = $this->receive($fp);
            
        //     // $input = $this->getUserInput();
        //     // if($input === 'exit'){
        //     //     exit;
        //     // }
        // }
    }

    private function getUserInput(){
        echo "Quelle commande souhaitez-vous lancer ?";
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        if(trim($line) === 'exit'){
            echo "ABORTING!\n";
            return "exit";
        } else {
            return "continu";
        }
    }

    public function closeStream($fp){
        fclose($fp);
    }

    private function fullread($fp, $count) {
        $readBuffer = "";
        
        while ($count > 0) {
            $data = fread($fp, $count);
            if ($data === FALSE) {
                die("ERROR: fread failed");
            }
            $count -= strlen($data);
            $readBuffer .= $data;
        }
        //var_dump($fp);
        return $readBuffer;
    }

    public function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function whoisDomain( $domain){
        $whois = shell_exec("whois $domain");
        echo '<pre>';
        return $whois;
    }

    
    private function receive($fp) {
        $data = $this->fullread($fp, 4);
        $count = unpack('N', $data);
        $count = $count[1];
        $buffer = $this->fullread($fp, $count - 4);
        return $buffer;
    }

    public function checkdomain($domain){
        $buffer3 =  "<?xml version='1.0' encoding='UTF-8'?><epp xmlns='urn:ietf:params:xml:ns:epp-1.0' >
           <command>
 <check>
<domain:check xmlns:domain='urn:ietf:params:xml:ns:domain-1.0'>
<domain:name>$domain</domain:name>
</domain:check>
 </check>
 <clTRID>dsfdkvbdsgcbdgsc</clTRID>
 </command>
            </epp>";
        fwrite($this->fp, pack('N', 4 + strlen($buffer3)));
        //printf("SENT\n%s\n:\n%s\n",$this->name , $buffer);
        fwrite($this->fp, $buffer3);
        $frame = $this->receive($this->fp);
        $xml = simplexml_load_string($frame);
        //sprintf("Response :\n%s\n", $frame);
        
        $parsed = $this->get_string_between($frame, 'avail="', '">');

//echo $parsed; // (result = dog)
        var_dump($parsed);
        return $parsed;
    }

public function cronjob_exists($command){

    $cronjob_exists=false;

    exec('crontab -l', $crontab);


    if(isset($crontab)&&is_array($crontab)){

        $crontab = array_flip($crontab);

        if(isset($crontab[$command])){

            $cronjob_exists=true;

        }

    }
    return $cronjob_exists;
}

    public function append_cronjob($command){

        if(is_string($command)&&!empty($command)&& $this->cronjob_exists($command)===FALSE){

            //add job to crontab
            //exec('crontab -u nicolas_candelon -l '.$command.' | crontab -', $output);
            //exec('echo -e "`crontab su nicolascandelon -l`\n'.$command.'" | crontab -', $output);
            //var_dump('crontab -l '.$command.' | crontab -');

            //file_put_contents( 'cronList.txt', '56 * * * * /usr/local/bin/php -q /Users/nicolas_candelon/Documents/Projects/EPP/meikai-sankioto/watcher.php' );
            exec( 'crontab cronList.txt' , $output);
        }

       return $output;
    }
}

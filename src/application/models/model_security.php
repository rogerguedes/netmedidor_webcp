<?php
class Model_Security extends CI_Model{
    private $maxAttemtps = 15;
    private $blockedTime = 3600;//secconds

    function __construct(){
        parent::__construct();
        $this->load->database();
    }

    function __destruct(){
        $this->db->close();
    }

    public function getBlockedTime(){
        return $this->blockedTime;
    }

    public function getLoginAttempts($ip){
        $query = "SELECT * FROM `login_attempts` WHERE ip = ?;";
        $bindingArray = array($ip);
        $result = $this->db->query($query, $bindingArray);
        $attempts = array();
        if($result->num_rows > 0){//if, for some mistake, the table has more than one entry for a single IP.
            foreach($result->result() as $row){
                $attempts[] = $row;
            }
        }
        return $attempts;
    }

    public function mayTryLogin($attempts){
        if( count($attempts) < 1 ){
            //let they try to login
            return true;
        }
        else{
            //verify if the tries are lower than attempts limit and time since first wrong login attempt fits the bloked time
            $attempt = $attempts[0];
            if( $attempt->tries >= $this->maxAttemtps && ( time() - strtotime($attempt->first_attempt) < $this->blockedTime ) ){//block the login attempt
                return false;
            }
            else{ //let it try to log in
                return true;
            }
        }
    }

    public function lookAtThisGuy($attempts){
        if(count( $attempts ) == 0){//if there is no attempts, just create the first.
            $this->load->library('protection');
            $queryString = "INSERT INTO `login_attempts` (`ip`, `first_attempt`, `tries`) VALUES (?, ?, 1);";
            $bindingArray = array( $this->protection->getUserIP() , date( "Y-m-d H:i:s", time() ) );
        }
        else{
            if( ( time() - strtotime( $attempts[0]->first_attempt ))  < $this->blockedTime ){//if the current attempt are inside a blockTime period after the first wrong attempt.
                $queryString = "UPDATE `login_attempts` SET `first_attempt`=?, `tries`=? WHERE `ip`=?;";//just increment the tries.
                $bindingArray = array( $attempts[0]->first_attempt, $attempts[0]->tries + 1 , $attempts[0]->ip );
            }
            else{
                $queryString = "UPDATE `login_attempts` SET `first_attempt`=?, `tries`=1 WHERE `ip`=?;";//renew the entry with the current time stamp and 1 tries.
                $bindingArray = array( date( "Y-m-d H:i:s", time() ) , $attempts[0]->ip );
            }

        }
        $this->db->query($queryString, $bindingArray);// pega dados da tabela Cadastros onde há esse login e senha
    }

    public function clearThisGuy($ip){
        $query = "DELETE FROM `login_attempts` WHERE `ip`=?;";
        $bindingArray = array($ip);
        $this->db->query($query, $bindingArray);// pega dados da tabela Cadastros onde há esse login e senha
    }
}
?>

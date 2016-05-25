<?php
    class Model_User extends CI_Model{
        function __construct(){
            parent::__construct();
            $this->load->database();
            include_once(APPPATH."beans/User.php");
        }
        
        function __destruct(){
            $this->db->close();
        }
        
        function getUserByEmail($email){
            $loginCheckQuery = "SELECT * FROM `users` WHERE email = ?;";
            $queryResult = $this->db->query($loginCheckQuery,array($email));// pega dados da tabela Cadastros onde há esse login e senha
            if($queryResult->num_rows == 1){//confirma se há 1 registro para esses dados
                foreach($queryResult->result() as $row){
                    $user = new User($row->id_users, $row->name, $row->email, $row->password, $row->access_level, $row->lang);
                }
                return $user;
            }
            else{
                return null;
            }
        }
        
        function validateUser($email,$password){
            $loginCheckQuery = "SELECT * FROM `users` WHERE email = ? AND password = ?;";
            $queryResult = $this->db->query($loginCheckQuery,array($email,$password));// pega dados da tabela Cadastros onde há esse login e senha
            if($queryResult->num_rows == 1){//confirma se há 1 registro para esses dados
                return true;
            }
            else{
                return false;
            }
        }
    }
?>

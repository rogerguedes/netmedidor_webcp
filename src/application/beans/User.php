<?php
    include_once("application/beans/MyObject.php");
	class User extends MyObject{

		private $ID;
		private $name;
		private $email;
		private $password;
		private $accessLevel;
		private $language;

        public function __construct($id, $name, $email, $pass, $accLvl, $lang){
			$this->ID = $id;
			$this->name = $name;
			$this->email = $email;
			$this->password = $pass;
            $this->accessLevel = $accLvl;
            $this->language = $lang;
        }

        public function getAsArray(){
            return get_object_vars($this);
        }

		public function getID(){
			return $this->ID;
		}
		
		public function setID($id){
			$this->ID = $id;
		}
		public function getName(){
			return $this->name;
		}
		
		public function setName($name){
			$this->name = $name;
		}
		
		public function getEmail(){
			return $this->email;
		}
		
		public function setEmail($email){
			$this->email = $email;
		}
		
		public function getPassword(){
			return $this->password;
		}
		
		public function setPassword($password){
			$this->password = $password;
		}
		
		public function getAccessLevel(){
			return $this->accessLevel;
		}
		
		public function setAccessLevel($param){
			$this->accessLevel = $param;
		}
		
        public function getLanguage(){
			return $this->language;
		}
		
		public function setLanguage($param){
			$this->language = $param;
        }
	}
?>

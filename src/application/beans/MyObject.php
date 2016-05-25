<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    abstract class MyObject{
        abstract function getAsArray();
        
        public function getAsJSON(){
            $var = $this->getAsArray();
            foreach($var as &$value){
                if(is_object($value) && method_exists($value,'getAsJSON')){
                    $value = $value->getAsJSON();
                }
            }
            return json_encode($var);
        }

        public function isNull(){
            $var = $this->getAsArray();
            foreach($var as &$value){
                if($value){
                    return false;
                }
            }
            return true;
        }
	}
?>

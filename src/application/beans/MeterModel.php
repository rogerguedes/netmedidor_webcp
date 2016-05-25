<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once("application/beans/MyObject.php");
class MeterModel extends MyObject{
    private $id;
    private $name;
    private $description;
    private $commands;
    
    public function __construct($param0, $param1, $param2, $param3){
        $this->id = $param0;
        $this->name = $param1;
        $this->description = $param2;
        $this->commands = $param3;
    }
    
    public function getAsArray(){
        $objArray = get_object_vars($this);
        foreach($objArray as &$objAtt){
            if(is_object($objAtt) && method_exists($objAtt,'getAsArray')){
                $objAtt = $objAtt->getAsArray();
            }else{
                if( is_array($objAtt) ){
                    foreach($objAtt as &$arrayObjAtt){
                        if(is_object($arrayObjAtt) && method_exists($arrayObjAtt,'getAsArray')){
                            $arrayObjAtt = $arrayObjAtt->getAsArray();
                        }
                    }
                }
            }
        }
        return $objArray;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($param){
        $this->id = $param;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($param){
        $this->name = $param;
    }

    public function getDescription(){
        return $this->description;
    }

    public function setDescription($param){
        $this->description = $param;
    }

    public function getCommands(){
        return $this->commands;
    }

    public function setCommands($param){
        $this->commands = $param;
    }
    
    public function pushCommand($param){
        $this->commands[] = $param;
    }
}
?>

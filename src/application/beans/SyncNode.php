<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once("application/beans/MyObject.php");
class SyncNode extends MyObject{
    public $id;
    public $model;
    public $netAddr;
    public $address;
    public $status;
    public $meters;

    public function __construct($param0, $param1, $param2, $param3, $param4, $param5){
        $this->id = $param0;
        $this->model = $param1;
        $this->netAddr = $param2;
        $this->address = $param3;
        $this->status = $param4;
        $this->meters = $param5;
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
}
?>

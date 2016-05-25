<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    function objArray2FullArray(&$inArray){
        foreach($inArray as &$value){
            if( is_array($value) ){
                objArray2FullArray($value);
            }
            else{
                if(is_object($value) && method_exists($value,'getAsArray')){
                    $value = $value->getAsArray();
                }
            }
        }
    }
    if( is_array($jsonData) ){
        objArray2FullArray($jsonData);
    }
    else{
        if(is_object($jsonData) && method_exists($jsonData,'getAsArray')){
            $jsonData = $jsonData->getAsArray();
        }
    }
    header("Content-Type: application/json; charset=\"utf-8\"");
    $jsonData = json_encode( $jsonData );
    echo $jsonData;
?>

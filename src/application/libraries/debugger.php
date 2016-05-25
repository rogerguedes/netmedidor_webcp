<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Debugger{
    public static $headerSent = false;
    function dump($var, $stop=false){
        if( ! self::$headerSent ){
            header("Content-Type: text/plain; charset=\"utf-8\"");
            self::$headerSent = true;
        }
        echo "DEBUG > ";
        var_dump($var);
        if( $stop ){
            echo "\nDEBUG: script execution stoped.";
            exit();
        }
    }
}

?>


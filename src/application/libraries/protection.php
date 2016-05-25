<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Protection{
    public function isUserLogged(){
        $CI =& get_instance();
        $CI->load->library('session');
        if($CI->session->userdata('email')){
            return true;
        }else{
            $CI->session->sess_destroy();//destroy the session created to verify the session var 'logged'.
            return false;
        }
    }
    public function hashPass($pass){
        return base64_encode(pack("H*", sha1(utf8_encode($pass))));
    }
    public function getUserIP() {
        //Just get the headers if we can or else use the SERVER global
        $CI =& get_instance();
        $CI->load->library('apphelper');
        $headers = $CI->apphelper->getRequestHeaders();
        //Get the forwarded IP if it exists
        if( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ){
            $the_ip = $headers['X-Forwarded-For'];
        }
        elseif( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )){
            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
        }
        else{
            $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
        }
        return $the_ip;
    }
}

?>

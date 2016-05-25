<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->library('protection');
        $this->load->library('apphelper');
        $this->load->library('form_validation');
    }
    
    public function seeView($view){
        if(isset($view) && $view != ""){
            $this->load->library('c3po');
            $data['glossary'] = &$this->c3po->getGlossary();
            $this->apphelper->loadDefaultViewData($data);
            $this->load->view("html/".$view,$data);
        }
    }
}

?>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Commands extends CI_Controller {

    public function __construct(){
        parent::__construct();
        include_once(APPPATH."beans/Command.php");
        $this->load->helper('form');
        $this->load->library('protection');
        $this->load->library('apphelper');
        $this->load->library('c3po');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '');
    }
    
    public function delete(){
        $data['status'] = null;
        $data['object'] = array();
        $data['errors'] = array();
        
        $isLogged = $this->protection->isUserLogged();
        
        $data['glossary'] = &$this->c3po->getGlossary();
        if($isLogged){
            $this->load->model('model_user');
            $user = $this->model_user->getUserByEmail( $this->session->userdata('email') );
            $data['glossary'] = &$this->c3po->getGlossary( $user->getLanguage() );
            $validationRules = array(
                array('field' => 'id', 'label' => 'ID', 'rules' => 'required|is_natural_no_zero')
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            $this->form_validation->set_message('is_natural_no_zero', $data['glossary']['VALIDATION']['IS_NATURAL_NO_ZERO']);
            if( $this->form_validation->run() ){
                $id = $this->input->post('id');
                $command = new Command($id, null, null, null);
                $data["status"] = true;
                $this->load->model('model_commands');
                $this->model_commands->delete( $command );
            }else{
                $data["status"] = false;
                foreach($validationRules as $field){
                    $error = form_error($field['field']);
                    if($error != ""){
                        $data["errors"][] = $error;
                    }
                }
            }
        }else{
            $data['status'] = false;
            $data['errors'][] = $data['glossary']['MSGS']['BAD_LOGIN'];
        }
        
        //prepare the answer
        $clientAccepts = $this->apphelper->getAcceptHeader();
        switch($clientAccepts[0]){
        case "text/html":
            $this->apphelper->loadDefaultViewData($data);
            if($isLogged){
                $viewName = 'html/view_metermodels.php';
                $data['sessionUser'] = $user; 
            }else{
                $data['glossaryList'] = $this->c3po->getGlossaryList();
                $viewName = 'html/view_login';
            }
            break;
        case "application/json":
            $viewName = 'json_render.php';
            $data['jsonData']['status'] = &$data['status'];
            $data['jsonData']['object'] = &$data['object'];
            $data['jsonData']['errors'] = &$data['errors'];
            break;
        default:
            $viewName = 'text_render.php';
            $data['text'] = $data['glossary']['MSGS']['UNKNOW_MIME'];
            break;
        }

        //sends the answer
        $this->load->view($viewName, $data);
    }
    
    public function update(){
        $data['status'] = null;
        $data['object'] = array();
        $data['errors'] = array();
        
        $isLogged = $this->protection->isUserLogged();
        
        $data['glossary'] = &$this->c3po->getGlossary();
        if($isLogged){
            $this->load->model('model_user');
            $user = $this->model_user->getUserByEmail( $this->session->userdata('email') );
            $data['glossary'] = &$this->c3po->getGlossary( $user->getLanguage() );
            $validationRules = array(
                array('field' => 'id', 'label' => 'ID', 'rules' => 'required|is_natural_no_zero')
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            $this->form_validation->set_message('is_natural_no_zero', $data['glossary']['VALIDATION']['IS_NATURAL_NO_ZERO']);
            if( $this->form_validation->run() ){
                $id = $this->input->post('id');
                $command = new Command($id, null, null, null);

                if( $this->input->post('name') ){
                    $command->setName( $this->input->post('name') );
                }
                
                if( $this->input->post('desc') ){
                    $command->setDescription( $this->input->post('desc') );
                }
                
                if( $this->input->post('query') ){
                    $command->setQuery( $this->input->post('query') );
                }
                
                $this->load->model('model_commands');
                $data["status"] = $this->model_commands->update( $command );
            }else{
                $data["status"] = false;
                foreach($validationRules as $field){
                    $error = form_error($field['field']);
                    if($error != ""){
                        $data["errors"][] = $error;
                    }
                }
            }
        }else{
            $data['status'] = false;
            $data['errors'][] = $data['glossary']['MSGS']['BAD_LOGIN'];
        }
        
        //prepare the answer
        $clientAccepts = $this->apphelper->getAcceptHeader();
        switch($clientAccepts[0]){
        case "text/html":
            $this->apphelper->loadDefaultViewData($data);
            if($isLogged){
                $viewName = 'html/view_metermodels.php';
                $data['sessionUser'] = $user; 
            }else{
                $data['glossaryList'] = $this->c3po->getGlossaryList();
                $viewName = 'html/view_login';
            }
            break;
        case "application/json":
            $viewName = 'json_render.php';
            $data['jsonData']['status'] = &$data['status'];
            $data['jsonData']['object'] = &$data['object'];
            $data['jsonData']['errors'] = &$data['errors'];
            break;
        default:
            $viewName = 'text_render.php';
            $data['text'] = $data['glossary']['MSGS']['UNKNOW_MIME'];
            break;
        }

        //sends the answer
        $this->load->view($viewName, $data);
    }
    
}

?>

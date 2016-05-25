<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SyncNodeModels extends CI_Controller {

    public function __construct(){
        parent::__construct();
        include_once(APPPATH."beans/SyncNodeModel.php");
        include_once(APPPATH."beans/Command.php");
        $this->load->library('protection');
        $this->load->library('apphelper');
        $this->load->library('c3po');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '');
        //$this->load->library('debugger'); //$this->debugger->dump($data, true);
    }

    public function index($lang='en-GB'){
        $this->apphelper->loadDefaultViewData($data);
        $data['glossary'] = &$this->c3po->getGlossary($lang);
        if($this->protection->isUserLogged()){
            $this->load->model('model_user');
            $user = $this->model_user->getUserByEmail( $this->session->userdata('email') );
            $data['glossary'] = &$this->c3po->getGlossary( $user->getLanguage() );
            $data['sessionUser'] = $user;
            $this->load->view('html/view_syncnodemodels.php',$data);
        }
        else{
            $data['glossaryList'] = $this->c3po->getGlossaryList();
            $this->load->view('html/view_login',$data);
        }
    }

    public function create(){
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
                array('field' => 'name', 'label' => $data['glossary']['GENERAL']['NAME'] , 'rules' => 'required'),
                array('field' => 'desc', 'label' => $data['glossary']['GENERAL']['DESCRIPTION'] , 'rules' => 'required')
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            if( $this->form_validation->run() ){
                $data["status"] = true;
                $newModel = new SyncNodeModel(null, $this->input->post('name'), $this->input->post('desc'), null);
                $this->load->model('model_syncnodemodels');
                $data["object"] = $this->model_syncnodemodels->create($newModel);
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
    
    public function read($id=null){
        $data['status'] = null;
        $data['object'] = array();
        $data['errors'] = array();
        
        $isLogged = $this->protection->isUserLogged();
        
        $data['glossary'] = &$this->c3po->getGlossary();
        if($isLogged){
            $this->load->model('model_user');
            $user = $this->model_user->getUserByEmail( $this->session->userdata('email') );
            $data['glossary'] = &$this->c3po->getGlossary( $user->getLanguage() );
            $this->load->model('model_syncnodemodels');
            if($id){//if there's an id
                if(preg_match("/^\d+$/i",$id, $matches)){//this id must to be only numbers
                    $data['status'] = true;
                    $data['object'] = $this->model_syncnodemodels->read( new SyncNodeModel($id, null, null, null) );
                }else{
                    $data['status'] = false;
                    $data['errors'][] = $data['glossary']['VALIDATION']['IS_NATURAL_NO_ZERO'];
                }
            }else{
                $data['status'] = true;
                $data['object'] = $this->model_syncnodemodels->read();
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
                array('field' => 'id', 'label' => $data['glossary']['GENERAL']['MODEL_ID'] , 'rules' => 'required|is_natural_no_zero')
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            $this->form_validation->set_message('is_natural_no_zero', $data['glossary']['VALIDATION']['IS_NATURAL_NO_ZERO']);
            if( $this->form_validation->run() ){
                $snModel = new SyncNodeModel($this->input->post('id'), null, null, null);

                if( $this->input->post('name') ){
                    $snModel->setName( $this->input->post('name') );
                }
                
                if( $this->input->post('desc') ){
                    $snModel->setDescription( $this->input->post('desc') );
                }
                
                $data["status"] = true;
                $this->load->model('model_syncnodemodels');
                $this->model_syncnodemodels->update( $snModel );
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
                array('field' => 'id', 'label' => $data['glossary']['GENERAL']['MODEL_ID'] , 'rules' => 'required|is_natural_no_zero')
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            $this->form_validation->set_message('is_natural_no_zero', $data['glossary']['VALIDATION']['IS_NATURAL_NO_ZERO']);
            if( $this->form_validation->run() ){
                $snModel = new SyncNodeModel($this->input->post('id'), null, null, null);
                $data["status"] = true;
                $this->load->model('model_syncnodemodels');
                $this->model_syncnodemodels->delete( $snModel );
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
    
    public function appendCmd(){
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
                array('field' => 'id', 'label' => $data['glossary']['GENERAL']['MODEL_ID'] , 'rules' => 'required|is_natural_no_zero'),
                array('field' => 'name', 'label' => $data['glossary']['GENERAL']['NAME'] ,'rules' => 'required'),
                array('field' => 'desc', 'label' => $data['glossary']['GENERAL']['DESCRIPTION'] ,'rules' => 'required'),
                array('field' => 'query', 'label' => $data['glossary']['APP']['QUERY_STRING'] , 'rules' => 'required')
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            $this->form_validation->set_message('is_natural_no_zero', $data['glossary']['VALIDATION']['IS_NATURAL_NO_ZERO']);
            if( $this->form_validation->run() ){
                $snModel = new SyncNodeModel($this->input->post('id'), null, null, null);
                $cmd = new Command(null, $this->input->post('name'), $this->input->post('desc'), $this->input->post('query'));

                $this->load->model('model_commands');
                $createdCmd = $this->model_commands->create( $cmd );
                
                $this->load->model('model_syncnodemodels');
                $this->model_syncnodemodels->appendCmd( $snModel, $createdCmd );
                
                $data["status"] = true;
                $data["object"] = $createdCmd;
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
    
    public function removeCmd(){
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
                array('field' => 'id_snm', 'label' => $data['glossary']['GENERAL']['MODEL_ID'] , 'rules' => 'required|is_natural_no_zero'),
                array('field' => 'id_cmd', 'label' => $data['glossary']['APP']['COMMAND_ID'] , 'rules' => 'required|is_natural_no_zero'),
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            $this->form_validation->set_message('is_natural_no_zero', $data['glossary']['VALIDATION']['IS_NATURAL_NO_ZERO']);
            if( $this->form_validation->run() ){
                $data["status"] = true;
                $snModel = new SyncNodeModel($this->input->post('id_snm'), null, null, null);
                $cmd = new Command($this->input->post('id_cmd'), null, null, null);
                $this->load->model('model_syncnodemodels');
                $this->model_syncnodemodels->removeCmd( $snModel, $cmd );
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

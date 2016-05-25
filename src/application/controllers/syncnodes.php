<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Syncnodes extends CI_Controller {

    public function __construct(){
        parent::__construct();
        include_once(APPPATH."beans/SyncNode.php");
        include_once(APPPATH."beans/SyncNodeModel.php");
        $this->load->library('protection');
        $this->load->library('apphelper');
        $this->load->library('c3po');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '');
        //$this->load->library('debugger'); //$this->debugger->dump($data, true);
    }

    public function index($lang='en-GB'){
        $data['status'] = null;
        $data['object'] = array();
        $data['errors'] = array();
        
        $isLogged = $this->protection->isUserLogged();
        
        $data['glossary'] = &$this->c3po->getGlossary($lang);
        
        $this->apphelper->loadDefaultViewData($data);
        if($this->protection->isUserLogged()){
            $this->load->model('model_user');
            $user = $this->model_user->getUserByEmail( $this->session->userdata('email') );
            $data['glossary'] = &$this->c3po->getGlossary( $user->getLanguage() );
            $data['sessionUser'] = $user;
            $this->load->view('html/view_syncnodes.php',$data);
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
                array('field' => 'id_snm', 'label' => $data['glossary']['GENERAL']['MODEL_ID'], 'rules' => 'required'),
                array('field' => 'netaddr', 'label' => $data['glossary']['GENERAL']['NET_ADDRESS'], 'rules' => 'required')
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            if( $this->form_validation->run() ){
                $this->load->model('model_syncnodemodels');
                $snModel = $this->model_syncnodemodels->read( new SyncNodeModel($this->input->post('id_snm'), null, null, null) );
                $syncNode = new SyncNode(null, $snModel, $this->input->post('netaddr'), null, null, null);
                if( $this->input->post('address') ){
                    $syncNode->address = $this->input->post('address');
                }
                $this->load->model('model_syncnodes');
                $data['object']  = $this->model_syncnodes->create( $syncNode, $snModel );
                $data["status"] = true;
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
                $viewName = 'html/view_not_implemented.php';
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
            $this->load->model('model_syncnodes');
            if($id){//if there's an id
                if(preg_match("/^\d+$/i",$id, $matches)){//this id must to be only numbers
                    $data['status'] = true;
                    $data['object'] = $this->model_syncnodes->read( new SyncNode($id, null, null, null, null, null) );
                }else{
                    $data['status'] = false;
                    $data['errors'][] = $data['glossary']['VALIDATION']['IS_NATURAL_NO_ZERO'];
                }
            }else{
                $data['status'] = true;
                $data['object'] = $this->model_syncnodes->read();
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
                $viewName = 'html/view_syncnodes.php';
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
                array('field' => 'id', 'label' => $data['glossary']['APP']['SYNCNODE_ID'], 'rules' => 'required|is_natural_no_zero')
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            $this->form_validation->set_message('is_natural_no_zero', $data['glossary']['VALIDATION']['IS_NATURAL_NO_ZERO']);
            if( $this->form_validation->run() ){
                $syncNode = new SyncNode($this->input->post('id'), null, null, null, null, null);

                if( $this->input->post('id_snm') ){
                    $this->load->model('model_syncnodemodels');
                    $syncNode->model = $this->model_syncnodemodels->read( new SyncNodeModel($this->input->post('id_snm'), null, null, null) );
                }
                
                if( $this->input->post('netaddr') ){
                    $syncNode->netAddr = $this->input->post('netaddr');
                }
                
                if( $this->input->post('address') ){
                    $syncNode->address = $this->input->post('address');
                }
                
                $this->load->model('model_syncnodes');
                $this->model_syncnodes->update( $syncNode );
                $data["status"] = true;
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
                array('field' => 'id', 'label' => $data['glossary']['APP']['SYNCNODE_ID'], 'rules' => 'required|is_natural_no_zero')
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            $this->form_validation->set_message('is_natural_no_zero', $data['glossary']['VALIDATION']['IS_NATURAL_NO_ZERO']);
            if( $this->form_validation->run() ){
                $syncNode = new SyncNode($this->input->post('id'), null, null, null, null, null);
                $this->load->model('model_syncnodes');
                $this->model_syncnodes->delete( $syncNode);
                $data["status"] = true;
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


    public function sendCmd(){
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
                array('field' => 'id_sn', 'label' => $data['glossary']['APP']['SYNCNODE_ID'] , 'rules' => 'required|is_natural_no_zero'),
                array('field' => 'id_cmd', 'label' => $data['glossary']['APP']['COMMAND_ID'] , 'rules' => 'required|is_natural_no_zero')
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            $this->form_validation->set_message('is_natural_no_zero', $data['glossary']['VALIDATION']['IS_NATURAL_NO_ZERO']);
            if( $this->form_validation->run() ){
                $id_sn = $this->input->post('id_sn');
                $id_cmd = $this->input->post('id_cmd');
                $this->load->model('model_syncnodes');
                $syncNode = $this->model_syncnodes->read( new SyncNode( $id_sn, null, null, null, null, null) );
                $selectedCommand = null;
                foreach( $syncNode->model->getCommands() as $cmd ){
                    if($cmd->getId() == $id_cmd){
                        $selectedCommand = $cmd;
                        break;
                    }
                }
                //var_dump( $selectedCommand );
                //var_dump( $syncNode->netAddr.$selectedCommand->getQuery() );
                //exit();

                //var_dump( $selectedCommand->getQuery() );
                $this->load->library('curl', array(
                    'url' => $syncNode->netAddr.$selectedCommand->getQuery()
                    //'url' => "200.129.11.75/sandbox/myXML.php"
                ));
                $this->curl->get();
                //var_dump( $this->curl->getInfo() );
                //exit();
                switch( $this->curl->getInfo()->http_code ){
                case 200:
                    $data["status"] = true;
                    $this->load->library('xmlutils');
                    $data['object'] = simplexml_load_string( $this->curl->getResponseBody() );
                    break;
                default:
                    $data["status"] = false;
                    $data["errors"][] = $syncNode->netAddr.$selectedCommand->getQuery(). " não responde;";//lang
                    break;
                }
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

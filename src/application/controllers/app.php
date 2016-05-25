<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

    public function __construct(){
        parent::__construct();
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
            $this->load->view('html/view_syncnodes.php',$data);// implementar ADM dashboard
        }
        else{
            $data['glossaryList'] = $this->c3po->getGlossaryList();
            $this->load->view('html/view_login',$data);
        }
    }
    
    public function login($lang='en-GB'){
        $data['status'] = null;
        $data['object'] = array();
        $data['errors'] = array();
        
        $clientIP = $this->protection->getUserIP();
        $this->load->model('model_security');
        $clientAttempts = $this->model_security->getLoginAttempts( $clientIP );
        $mayClientTryLogin = $this->model_security->mayTryLogin( $clientAttempts );
        $loginSubmitOk = false;
        $loginCheck = false;

        $data['glossary'] = &$this->c3po->getGlossary($lang);

        if( $mayClientTryLogin ){
            $validationRules = array(
                array('field' => 'email', 'label' => $data['glossary']['GENERAL']['EMAIL'], 'rules' => 'required'),
                array('field' => 'password', 'label' => $data['glossary']['GENERAL']['PASSWORD'], 'rules' => 'required')
            );
            $this->form_validation->set_rules( $validationRules );
            $this->form_validation->set_message('required', $data['glossary']['VALIDATION']['REQUIRED']);
            $loginSubmitOk = $this->form_validation->run();
            if( $loginSubmitOk ){
                $email = $this->input->post('email');
                $password = $this->protection->hashPass($this->input->post('password'));
                $this->load->model('model_user');
                $loginCheck = $this->model_user->validateUser($email,$password);
                if( $loginCheck ){
                    if( count( $clientAttempts ) > 0 ){
                        $this->model_security->clearThisGuy($clientIP);
                    }
                    $this->load->library('session');
                    $this->session->set_userdata('email',$email);
                    $data['status'] = true;
                }else{
                    $data['status'] = false;
                    $data['errors'][] = $data['glossary']['MSGS']['BAD_LOGIN'];
                    $this->model_security->lookAtThisGuy( $clientAttempts );
                }
            }else{
                $data['status'] = false;
                foreach($validationRules as $field){
                    $error = form_error($field['field']);
                    if($error != ""){
                        $data["errors"][] = $error;
                    }
                }
            }
        }
        else{
            $data['status'] = false;
            $data['errors'][] = "You are blocked for ".$this->model_security->getBlockedTime()." seconds.";
        }
        //prepare the answer
        $clientAccepts = $this->apphelper->getAcceptHeader();
        switch($clientAccepts[0]){
        case "text/html":
            $this->apphelper->loadDefaultViewData($data);
            if( $mayClientTryLogin ){
                if( $loginSubmitOk && $loginCheck ){
                        $data['sessionUser'] = $this->model_user->getUserByEmail($email);
                        header("Location: ".base_url());
                        exit();
                }else{
                    $data['glossaryList'] = $this->c3po->getGlossaryList();
                    $viewName = 'html/view_login';
                }
            }else{
                $data["blockedTime"] = $this->model_security->getBlockedTime();
                $viewName = 'html/view_blocked';
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
    
    public function logout($lang='en-GB'){
        $this->load->library('session');
        $this->session->sess_destroy();
        $this->apphelper->loadDefaultViewData($data);
        $data['glossary'] = &$this->c3po->getGlossary($lang);
        $data['glossaryList'] = $this->c3po->getGlossaryList();
        $this->load->view('html/view_login',$data);
    }
}

?>

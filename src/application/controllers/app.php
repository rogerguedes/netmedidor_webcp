<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('protection');
        $this->load->library('apphelper');
        $this->load->library('form_validation');
    }
    
    public function index(){
        $this->apphelper->loadDefaultViewData($data);
        if($this->protection->isUserLogged()){
            $this->load->model('model_user');
            $email = $this->session->userdata('email');
            $data['sessionUser'] = $this->model_user->getUserByEmail($email);
            $this->load->view('html/dash_board.php',$data);// implementar ADM dashboard
        }
        else{
            $this->apphelper->loadLoginInputs($data);
            $this->load->view('html/view_login',$data);
        }
    }
    
    public function login(){
        $data['status'] = null;
        $data['object'] = array();
        $data['errors'] = array();
        
        $clientIP = $this->protection->getUserIP();
        $this->load->model('model_security');
        $clientAttempts = $this->model_security->getLoginAttempts( $clientIP );
        $mayClientTryLogin = $this->model_security->mayTryLogin( $clientAttempts );
        $loginSubmitOk = false;
        $loginCheck = false;

        if( $mayClientTryLogin ){
            $this->form_validation->set_rules('email','Email','required');//setting a form_validation rule to the field 'name'
            $this->form_validation->set_rules('password','Password','required');//setting a form_validation rule to the field 'name'
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
                    $data['errors'][] = "Wrong login or password.";
                    $this->model_security->lookAtThisGuy( $clientAttempts );
                }
            }else{
                $data['status'] = false;
                $data['errors'][] = "Blank username or login.";
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
                        $this->load->library('session');
                        $this->session->set_userdata('email',$email);
                        $data['sessionUser'] = $this->model_user->getUserByEmail($email);
                        header("Location: ".base_url());
                        exit();
                }else{
                    $viewName = 'html/view_login';
                    $this->apphelper->loadLoginInputs($data);
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
            $data['text'] = $this->apphelper->getErrMsgs()['unknowMIME'];
            break;
        }
        //sends the answer
        $this->load->view($viewName, $data);
    }
    
    public function logout(){
        $data['status'] = null;
        $data['object'] = array();
        $data['errors'] = array();
        
        $this->load->library('session');
        $this->session->sess_destroy();
        
        $data['status'] = true;
        
        $clientAccepts = $this->apphelper->getAcceptHeader();
        switch($clientAccepts[0]){
        case "text/html":
            $this->apphelper->loadDefaultViewData($data);
            $this->apphelper->loadLoginInputs($data);
            $viewName = 'html/view_login';
            break;
        case "application/json":
            $viewName = 'json_render.php';
            $data['jsonData']['status'] = &$data['status'];
            $data['jsonData']['object'] = &$data['object'];
            $data['jsonData']['errors'] = &$data['errors'];
            break;
        default:
            $viewName = 'text_render.php';
            $data['text'] = $this->apphelper->getErrMsgs()['unknowMIME'];
            break;
        }
        //sends the answer
        $this->load->view($viewName, $data);
    }
    
    public function checkLogin(){
        $data['status'] = null;
        $data['object'] = null;
        $data['errors'] = array();
        
        if($this->protection->isUserLogged()){
            $this->load->model('model_user');
            $email = $this->session->userdata('email');
            $data['status'] = true;
            $data['object'] = $this->model_user->getUserByEmail($email);
        }
        else{
            $data['status'] = false;
        }
        
        //prepare the answer
        $clientAccepts = $this->apphelper->getAcceptHeader();
        switch($clientAccepts[0]){
        case "text/html":
            $this->apphelper->loadDefaultViewData($data);
            var_dump($data);
            exit();
            break;
        case "application/json":
            $viewName = 'json_render.php';
            $data['jsonData']['status'] = &$data['status'];
            $data['jsonData']['object'] = &$data['object'];
            $data['jsonData']['errors'] = &$data['errors'];
            break;
        default:
            $viewName = 'text_render.php';
            $data['text'] = $this->apphelper->getErrMsgs()['unknowMIME'];
            break;
        }
        //sends the answer
        $this->load->view($viewName, $data);
    }
}

?>

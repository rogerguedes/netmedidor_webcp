<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CURL{

    private $url;
    private $curlInstance;
    private $requestHeaders;
    private $responseBody;
    private $responseHeaders;

    public function __construct($params){
        $this->url = $params['url'];
        $this->curlInstance = curl_init($this->url);
        curl_setopt($this->curlInstance, CURLOPT_RETURNTRANSFER, true);//makes curl return the page content instead print it.
        curl_setopt($this->curlInstance, CURLOPT_HEADER, true);//makes curl_exec() return the response headers beyond the response body
    }

    public function __destruct(){
        curl_close($this->curlInstance);
    }

    public function setRequestHeaders($rHeaders){
        $this->requestHeaders = $rHeaders;
        curl_setopt($this->curlInstance, CURLOPT_HTTPHEADER, $this->requestHeaders);//append request headers to the request 
    }

    public function getRequestHeaders(){
        return $this->requestHeaders;
    }

    public function addRequestHeader($newHeader){
        $this->requestHeaders[] = $newHeader;
        curl_setopt($this->curlInstance, CURLOPT_HTTPHEADER, $this->requestHeaders);//append request headers to the request 
    }

    public function getResponseHeaders(){
        return $this->responseHeaders;
    }

    public function getResponseBody(){
        return $this->responseBody;
    }

    private function splitResponse($response){
        list($this->responseHeaders, $this->responseBody) = explode("\r\n\r\n", $response, 2);//split the response, divinding the response headers from response body
        $this->responseHeaders=$this->responseHeaders."\n";
    }

    public function get($data=null, $dataType=null){
        $tempResponse = curl_exec($this->curlInstance);
        if($tempResponse){
            $this->splitResponse($tempResponse);
        }
    }

    public function getInfo(){
        $tempObject = new stdClass();
        foreach (curl_getinfo($this->curlInstance) as $key => $value){
            $tempObject->$key = $value;
        }
        return $tempObject;
    }
}
?>


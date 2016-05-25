<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class C3PO{

    private $glossary = null;

    public function &getGlossary($lang='en-GB'){
        if( is_readable(APPPATH."/config/language/".$lang.".ini") ){
            if( ! isset($this->glossary[$lang]) ){
                $this->glossary[$lang] = parse_ini_file(APPPATH."/config/language/".$lang.".ini", true);
            }
            return $this->glossary[$lang];
        }
        else{
            exit('this language does not exist;');
        }
    }
    
    public function getGlossaryList(){
        return json_decode(file_get_contents(APPPATH."/config/language/lang.json"), true);
    }
}
?>

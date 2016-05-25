<?php
class Model_Commands extends CI_Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
        include_once(APPPATH."beans/Command.php");
    }

    function __destruct(){
        $this->db->close();
    }

    function create($newObj){
        $queryString = "INSERT INTO commands (name, description, query) VALUES (?, ?, ?);";
        $queryBinds = array($newObj->getName(), $newObj->getDescription(), $newObj->getQuery() );
        $queryResult = $this->db->query($queryString, $queryBinds);
        
        $queryString = "
            SELECT cmds.id_cmd as cmdID,
                cmds.name as cmdName,
                cmds.description as cmdDesc,
                cmds.query as cmdQuery
            FROM commands as cmds
            WHERE cmds.id_cmd = ?;";
        $queryBinds = array( $this->db->insert_id() );
        $queryResult = $this->db->query($queryString, $queryBinds)->result()[0];
        return new Command( $queryResult->cmdID, $queryResult->cmdName, $queryResult->cmdDesc, $queryResult->cmdQuery );
    }
    
    function update($obj){
        $id = $obj->getId();
        $obj->setId( null );
        if( !$id || $obj->isNull() ){
            return null;
        }
        
        $obj->setId( $id );

        $queryString = "UPDATE commands SET ";
        
        $queryBinds = array();
        
        if( $obj->getName() ){
            $queryString .= "name=?," ;
            $queryBinds[] = $obj->getName();
        }

        if( $obj->getDescription() ){
            $queryString .= "description=?," ;
            $queryBinds[] = $obj->getDescription();
        }

        if( $obj->getQuery() ){
            $queryString .= "query=?," ;
            $queryBinds[] = $obj->getQuery();
        }

        $queryBinds[] = $obj->getId();
        
        $queryString = substr($queryString, 0, -1)." WHERE id_cmd=?;";

        
        $queryResult = $this->db->query($queryString, $queryBinds);
        return $queryResult;
    }
    
    function delete($obj){
        $queryString = "DELETE FROM commands WHERE id_cmd = ?;";
        $queryBinds = array( $obj->getId() );
        return $this->db->query($queryString, $queryBinds);
    }
}
?>

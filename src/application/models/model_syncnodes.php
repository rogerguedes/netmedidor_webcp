<?php
class Model_SyncNodes extends CI_Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
        include_once(APPPATH."beans/SyncNode.php");
        include_once(APPPATH."beans/SyncNodeModel.php");
    }

    function __destruct(){
        $this->db->close();
    }

    function create($newObj, $model){
        if( !$newObj->netAddr || !$model->getId() ){
            return null;
        }
        $queryString = "INSERT INTO sync_nodes ";
        
        $queryColumns = array('names'=>'(', 'values'=>'(');

        $queryBinds = array();
        if( $newObj->netAddr ){
            $queryColumns['names'] .= "netAddr,";
            $queryColumns['values'] .= "?,";
            $queryBinds[] = $newObj->netAddr;
        }
        
        if( $newObj->address ){
            $queryColumns['names'] .= "address,";
            $queryColumns['values'] .= "?,";
            $queryBinds[] = $newObj->address;
        }

        if( $model->getId() ){
            $queryColumns['names'] .= "fk_snm,";
            $queryColumns['values'] .= "?,";
            $queryBinds[] = $model->getId();
        }
        
        $queryColumns['names'][strlen($queryColumns['names'])-1]=')';
        $queryColumns['values'][strlen($queryColumns['values'])-1]=')';

        $queryString .= $queryColumns['names']." VALUES ".$queryColumns['values'].";";

        $queryResult = $this->db->query($queryString, $queryBinds);


        $queryString = "SELECT
                sn.id as snID,
                sn.netAddr as snNetaddr,
                sn.address as snAddress
            FROM
                sync_nodes as sn
            WHERE
                sn.id = ?;";
        $queryBinds = array( $this->db->insert_id() );
        $queryResult = $this->db->query($queryString, $queryBinds)->result()[0];
        return new SyncNode( $queryResult->snID, $model, $queryResult->snNetaddr, $queryResult->snAddress, null, null);
        //var_dump( $this->db->last_query() );
        //exit();
    }

    function read($obj=null){
        $queryString = "SELECT
                sn.id as snID,
                sn.netAddr as snNetaddr,
                sn.address as snAddress,
                sn.fk_snm as fkSnm
            FROM
                sync_nodes as sn
            WHERE";
        $queryBinds = array();
        if( $obj ){
            $queryString .= " sn.id=?;";
            $queryBinds[] = $obj->id;
        }else{
            $queryString .= " TRUE;";
        }
        $queryResult = $this->db->query($queryString, $queryBinds);// pega dados da tabela Cadastros onde há esse login e senha
        if($queryResult->num_rows > 0){
            $syncNodes = array();
            $this->load->model('model_syncnodemodels');
            foreach($queryResult->result() as $row){
                $rowModel = $this->model_syncnodemodels->read( new SyncNodeModel( $row->fkSnm, null, null, null ) );
                $syncNodes[] = new SyncNode( $row->snID, $rowModel, $row->snNetaddr, $row->snAddress, null, array());
            }
            if($obj){
                return $syncNodes[0];
            }else{
                return $syncNodes;
            }
        }
        else{
            return null;
        }
    }
    
    function update($obj){
        $id = $obj->id;
        $obj->id = null;
        if( !$id || $obj->isNull() ){
            return null;
        }
        
        $obj->id = $id;

        $queryString = "UPDATE sync_nodes SET ";
        
        $queryBinds = array();
        
        if( $obj->netAddr ){
            $queryString .= "netAddr=?," ;
            $queryBinds[] = $obj->netAddr;
        }

        if( $obj->address ){
            $queryString .= "address=?," ;
            $queryBinds[] = $obj->address;
        }
        
        if( $obj->model ){
            $queryString .= "fk_snm=?," ;
            $queryBinds[] = $obj->model->getId();
        }

        $queryBinds[] = $obj->id;
        
        $queryString = substr($queryString, 0, -1)." WHERE id=?;";

        
        $queryResult = $this->db->query($queryString, $queryBinds);
        return $queryResult;
        //var_dump( $this->db->last_query() );
        //exit();
    }
    
    function delete($obj){
        if( !$obj->id ){
            return null;
        }
        $queryString = "DELETE FROM sync_nodes WHERE id = ?;";
        $queryBinds = array($obj->id);
        $queryResult = $this->db->query($queryString, $queryBinds);
        return $queryResult;
    }
}
?>

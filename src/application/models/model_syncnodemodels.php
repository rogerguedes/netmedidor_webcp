<?php
class Model_SyncNodeModels extends CI_Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
        include_once(APPPATH."beans/SyncNodeModel.php");
        include_once(APPPATH."beans/Command.php");
    }

    function __destruct(){
        $this->db->close();
    }

    function create($newObj){
        $queryString = "INSERT INTO sync_node_models ";
        
        $queryColumns = array('names'=>'(', 'values'=>'(');

        $queryBinds = array();
        if( $newObj->getName() ){
            $queryColumns['names'] .= "name,";
            $queryColumns['values'] .= "?,";
            $queryBinds[] = $newObj->getName();
        }

        if( $newObj->getDescription() ){
            $queryColumns['names'] .= "description,";
            $queryColumns['values'] .= "?,";
            $queryBinds[] = $newObj->getDescription();
        }

        
        $queryColumns['names'][strlen($queryColumns['names'])-1]=')';
        $queryColumns['values'][strlen($queryColumns['values'])-1]=')';

        $queryString .= $queryColumns['names']." VALUES ".$queryColumns['values'].";";
        
        $queryResult = $this->db->query($queryString, $queryBinds);


        $queryString = "SELECT
                snm.id as snmID,
                snm.name as snmName,
                snm.description as snmDesc
            FROM
                sync_node_models as snm
            WHERE
                snm.id = ?;";
        $queryBinds = array( $this->db->insert_id() );
        $queryResult = $this->db->query($queryString, $queryBinds)->result()[0];
        return new SyncNodeModel( $queryResult->snmID, $queryResult->snmName, $queryResult->snmDesc, null );
        //var_dump( $this->db->last_query() );
        //exit();
    }

    function read($obj=null){
        $queryString = "SELECT
                snm.id as snmID,
                snm.name as snmName,
                snm.description as snmDesc,
                cmd.id_cmd as cmdID,
                cmd.name as cmdName,
                cmd.description as cmdDesc,
                cmd.query as cmdQuery
            FROM
                sync_node_models as snm
                LEFT JOIN
                snm_has_cmds as shc ON snm.id = shc.fk_snm
                LEFT JOIN
                commands as cmd ON shc.fk_cmd = cmd.id_cmd
            WHERE ";
        $queryBinds = array();
        if( $obj ){
            $queryString .= " snm.id=?;";
            $queryBinds[] = $obj->getId();
        }else{
            $queryString .= " TRUE;";
        }
        $queryResult = $this->db->query($queryString, $queryBinds);// pega dados da tabela Cadastros onde há esse login e senha
        if($queryResult->num_rows > 0){//confirma se há 1 registro para esses dados
            $modelsHash = null;
            foreach($queryResult->result() as $row){
                if( isset( $modelsHash[$row->snmID] ) && $row->cmdID ){
                    $modelsHash[$row->snmID]->pushCommand( new Command( $row->cmdID, $row->cmdName, $row->cmdDesc, $row->cmdQuery ) );
                }else{
                    $commands = array();
                    if( $row->cmdID ){
                        $commands[] = new Command( $row->cmdID, $row->cmdName, $row->cmdDesc, $row->cmdQuery );
                    }
                    $modelsHash[$row->snmID] = new SyncNodeModel($row->snmID, $row->snmName, $row->snmDesc, $commands );
                }
            }

            $syncNodeModels = array();
            foreach($modelsHash as &$model){
                $syncNodeModels[] = $model;
            }
            if($obj){
                return $syncNodeModels[0];
            }else{
                return $syncNodeModels;
            }
        }
        else{
            return null;
        }
    }
    
    function update($obj){
        $id = $obj->getId();
        $obj->setId( null );
        if( !$id || $obj->isNull() ){
            return null;
        }
        
        $obj->setId( $id );

        $queryString = "UPDATE sync_node_models SET ";
        
        $queryBinds = array();
        
        if( $obj->getName() ){
            $queryString .= "name=?," ;
            $queryBinds[] = $obj->getName();
        }

        if( $obj->getDescription() ){
            $queryString .= "description=?," ;
            $queryBinds[] = $obj->getDescription();
        }

        $queryBinds[] = $obj->getId();
        
        $queryString = substr($queryString, 0, -1)." WHERE id=?;";

        
        $queryResult = $this->db->query($queryString, $queryBinds);
        return $queryResult;
        //var_dump( $this->db->last_query() );
        //exit();
    }
    
    function delete($obj){
        if( !$obj->getId() ){
            return null;
        }
        //Deleting the commands entries and, by cascade, mm_as_cmds.
        $queryString = "DELETE cmd
            FROM
                sync_node_models as snm LEFT JOIN
                snm_has_cmds as snmhc ON snm.id = snmhc.fk_snm LEFT JOIN
                commands as cmd ON snmhc.fk_cmd = cmd.id_cmd
            WHERE
                snm.id = ?;";
        $queryBinds = array($obj->getId());
        
        $queryResult = $this->db->query($queryString, $queryBinds);
        //Deleting the meter model itself.
        $queryString = "DELETE FROM sync_node_models WHERE id = ?;";
        $queryResult = $this->db->query($queryString, $queryBinds);
        return $queryResult;
    }
    
    function appendCmd($model, $command){
        if( !$model->getId() || !$command->getId() ){
            return null;
        }
        
        $queryString = "INSERT INTO snm_has_cmds (fk_snm, fk_cmd) VALUES (?, ?);";
        $queryBinds = array( $model->getId(), $command->getId() );
        $queryResult = $this->db->query($queryString, $queryBinds);
        return $queryResult;
    }

    function removeCmd($model, $command){
        if( !$model->getId()  || !$command->getId() ){
            return null;
        }

        $queryString = "DELETE FROM snm_has_cmds WHERE fk_snm = ? AND fk_cmd = ?;";
        $queryBinds = array( $model->getId(), $command->getId() );
        $queryResult = $this->db->query($queryString, $queryBinds);
        
        $queryString = "SELECT  COUNT(*) as count
            FROM commands as cmd
                INNER JOIN snm_has_cmds as snmhc
                ON snmhc.fk_cmd = cmd.id_cmd
            WHERE snmhc.fk_cmd = ?;";
        $queryBinds = array( $command->getId() );
        $remainingAssoc = (int) $this->db->query($queryString, $queryBinds)->result()[0]->count;
        if( $remainingAssoc == 0 ){
            $this->load->model('model_commands');
            $this->model_commands->delete( $command );
        }

        return $queryResult;
    }

}
?>

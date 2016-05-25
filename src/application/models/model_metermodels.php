<?php
class Model_MeterModels extends CI_Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
        include_once(APPPATH."beans/MeterModel.php");
        include_once(APPPATH."beans/Command.php");
    }

    function __destruct(){
        $this->db->close();
    }

    function create($newObj){
        $queryString = "INSERT INTO meter_models ";
        
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
        return $queryResult;
        //var_dump( $this->db->last_query() );
        //exit();
    }

    function read($obj=null){
        //$queryString = "SELECT * FROM meter_models WHERE";
        $queryString = "
        SELECT mm.id_mm as meterModelID,
            mm.name as meterModelName,
            mm.description as meterModelDescription,
            cmd.id_cmd as CommandID,
            cmd.name as CommandName,
            cmd.description as CommandDescription,
            cmd.query as CommandQuery
        FROM netmedidor.meter_models as mm
            LEFT JOIN netmedidor.mm_has_cmds as mhc
            ON
            mm.id_mm = mhc.fk_mm
            LEFT JOIN netmedidor.commands as cmd
            ON
            mhc.fk_cmd = cmd.id_cmd
        WHERE ";
        $queryBinds = array();
        if( $obj ){
            $queryString .= " id_mm=?;";
            $queryBinds[] = $obj->getId();
        }else{
            $queryString .= " TRUE;";
        }
        $queryResult = $this->db->query($queryString, $queryBinds);// pega dados da tabela Cadastros onde há esse login e senha
        if($queryResult->num_rows > 0){//confirma se há 1 registro para esses dados
            $modelsHash = null;
            foreach($queryResult->result() as $row){
                if( isset( $modelsHash[$row->meterModelID] ) && $row->CommandID ){
                    $modelsHash[$row->meterModelID]->pushCommand( new Command( $row->CommandID, $row->CommandName, $row->CommandDescription, $row->CommandQuery ) );
                }else{
                    $commands = array();
                    if( $row->CommandID ){
                        $commands[] = new Command( $row->CommandID, $row->CommandName, $row->CommandDescription, $row->CommandQuery );
                    }
                    $modelsHash[$row->meterModelID] = new MeterModel($row->meterModelID, $row->meterModelName, $row->meterModelDescription, $commands );
                }
            }

            $meterModels = array();
            foreach($modelsHash as &$model){
                $meterModels[] = $model;
            }
            if($obj){
                return $meterModels[0];
            }else{
                return $meterModels;
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

        //$queryString = "UPDATE meter_models SET name='11111asd', description='111111111qweqe' WHERE id_mm='10'";
        $queryString = "UPDATE meter_models SET ";
        
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
        
        $queryString = substr($queryString, 0, -1)."WHERE id_mm=?;";

        
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
            FROM meter_models as mm
                LEFT JOIN mm_has_cmds as mhc
                ON
                mm.id_mm = mhc.fk_mm
                LEFT JOIN commands as cmd
                ON
                mhc.fk_cmd = cmd.id_cmd
            WHERE
                id_mm = ?;";
        $queryBinds = array($obj->getId());
        
        $queryResult = $this->db->query($queryString, $queryBinds);
        //Deleting the meter model itself.
        $queryString = "DELETE FROM meter_models WHERE id_mm = ?;";
        $queryResult = $this->db->query($queryString, $queryBinds);
        return $queryResult;
    }
    
    function appendCmd($model, $command){
        if( !$model->getId() || !$command->getId() ){
            return null;
        }
        
        $queryString = "INSERT INTO mm_has_cmds (fk_mm, fk_cmd) VALUES (?, ?);";
        $queryBinds = array( $model->getId(), $command->getId() );
        $queryResult = $this->db->query($queryString, $queryBinds);
        return $queryResult;
    }

    function removeCmd($model, $command){
        if( !$model->getId()  || !$command->getId() ){
            return null;
        }

        $queryString = "DELETE FROM mm_has_cmds WHERE fk_mm = ? AND fk_cmd = ?;";
        $queryBinds = array( $model->getId(), $command->getId() );
        $queryResult = $this->db->query($queryString, $queryBinds);
        
        $queryString = "SELECT  COUNT(*) as count
            FROM commands as cmd
                INNER JOIN mm_has_cmds as mhc
                ON mhc.fk_cmd = cmd.id_cmd
            WHERE mhc.fk_cmd = ?;";
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

<?php
namespace Api\Core;
use \PDO;

class Database extends PDO {
    public function __construct(){
        $init_arr = array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+00:00'");
        parent::__construct(DB_TYPE.':host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS, $init_arr);
        if(DEBUG_MODE){
            parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public function select($query, $params = array(), $fetch_mode = PDO::FETCH_ASSOC){
        try{
            ksort($params);
            $st = $this->prepare($query);
            foreach($params as $key=>$value){
                $st->bindValue("$key", $value);
            }
            $st->execute();

            return $st->fetchAll($fetch_mode);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function insert($table, $params){
        try{
            ksort($params);
            $keys = implode(',', array_keys($params));
            $values = ':' . implode(',:', array_keys($params));

            $this->beginTransaction();
            $st = $this->prepare("INSERT INTO $table ($keys) VALUES ($values)");
            foreach($params as $key=>$value){
                $st->bindValue(":$key", $value);
            }
            $st->execute();
            $inserted_id = $this->lastInsertId();
            $this->commit();

            return $inserted_id;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function update($table, $params, $where){
        try{
            ksort($params);
            $values = '';
            foreach($params as $key=>$value){
                $values .= "`$key`=:$keys";
            }
            $field_details = rtrim($field_details, ',');

            $st = $this->prepare("UPDATE $table SET $values WHERE $where");
            foreach($params as $key=>$value){
                $st->bindValue(":$key", $value);
            }

            return $st->execute();
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function delete($table, $where, $params){
        try{
            ksort($params);
            $st = $this->prepare("DELETE FROM $table WHERE $where");
            foreach($params as $key=>$value){
                $st->bindValue("$key", $value);
            }
            
            return $st->execute();
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function procedure($name, $params = [], $fetch_mode = PDO::FETCH_ASSOC){
        try{
            //ksort($params);
            $procedure_params = '';
            foreach ($params as $key => $value) {
                $procedure_params .= ":$key,";
            }
            $procedure_params = rtrim($procedure_params, ',');

            $st = $this->prepare("CALL $name($procedure_params)");
            foreach($params as $key=>$value){
                $st->bindValue(":$key", $value);
            }
            $st->execute();
            
            return $st->fetchAll($fetch_mode);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
}
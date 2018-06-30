<?php

class Database extends PDO {
    public function __construct(){
        parent::__construct(DB_TYPE.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
        if(DEBUG_MODE){
            parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public function select($query, $params = array(), $fetchMode = PDO::FETCH_ASSOC){
        try{
            ksort($params);
            $st = $this->prepare($query);
            foreach($params as $key=>$value){
                $st->bindParam("$key", $value);
            }
            $st->execute();

            return $st->fetchAll($fetchMode);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function insert($table, $params){
        try{
            ksort($params);
            $keys = implode(",", array_keys($params));
            $values = ":" . implode(",:", array_keys($params));

            $this->beginTransaction();
            $st = $this->prepare("INSERT INTO $table ($keys) VALUES ($values)");
            foreach($params as $key=>$value){
                $st->bindParam(":$key", $value);
            }
            $st->execute();
            $insertedId = $this->lastInsertId();
            $this->commit();

            return $insertedId;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function update($table, $params, $where){
        try{
            ksort($params);
            $values = "";
            foreach($params as $key=>$value){
                $values .= $key . "=:$keys";
            }

            $st = $this->prepare("UPDATE $table SET $values WHERE $where");
            foreach($params as $key=>$value){
                $st->bindParam(":$key", $value);
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
                $st->bindParam("$key", $value);
            }
            
            return $st->execute();
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function procedure($name, $params = [], $fetchMode = PDO::FETCH_ASSOC){
        try{
            //ksort($params);
            $keys = [];
            foreach ($params as $key => $value) {
                array_push($keys, $key);
            }

            $keys = ":" . implode(",:", $keys);
            //$values = ":" . implode(",:", array_keys($params));

            $st = $this->prepare("CALL $name($keys)");
            foreach($params as $key=>$value){
                $st->bindParam(":$key", $value);
            }
            $st->execute();
            
            return $st->fetchAll($fetchMode);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
}
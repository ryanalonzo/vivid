<?php

class Vivid {
    protected $db;
    protected $query;
    protected $table;
    protected $results;
    protected $limit;

    function __construct($host,$database_name,$username,$password)
    {
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$database_name",$username,$password);
        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    function table($table)
    {
        $this->table = $table;
        return $this;
    }

    function limit(int $limit) {
        $this->limit = $limit;
        return $this;
    }

    function get()
    {
        if(isset($this->limit)) {
            $this->sql = "SELECT * FROM $this->table LIMIT $this->limit";
        } else {
            $this->sql = "SELECT * FROM $this->table";
        }
        try {
            $this->query = $this->db->prepare($this->sql);
            $this->query->execute();
            $this->results = $this->query->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            return $e->getMessage();
        }

        return $this->results;
    }

    function create($columns = [])
    {
        if(isset($this->table) && count($columns)) {
            $keys = array_keys($columns);
            $values = array_values($columns);

            $x = 1;
            $params = '';

            foreach($columns as $cols) {
                $params .= '?';
                if($x < count($columns)) {
                    $params .= ', ';
                }
                $x++;
            }

            $this->sql = "INSERT INTO {$this->table} (".implode(',', $keys).") VALUES ($params)";

            try {
                $query = $this->query = $this->db->prepare($this->sql);

                $par = 1;

                foreach($values as $val) {
                    $query->bindValue($par, $val);
                    $par++;
                }

                $this->query->execute();
            } catch(PDOException $e) {
                return $e->getMessage();
            }
        }
    }

    function delete($table, $id)
    {
        $this->sql = "DELETE FROM {$table} WHERE id = ?";

        try {
            $this->query = $this->db->prepare($this->sql);
            $this->query->execute(array($id));
        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    function update($columns = [], $id)
    {
        $set = '';
        $comma = 1;

        foreach($columns as $key => $value) {
            $set.= "{$key} = '$value'";
            if($comma < count($columns)) {
                $set .= ',';
            }
            $comma++;
        }

        $this->sql = "UPDATE {$this->table} SET {$set} WHERE id = ?";

        try {
            $this->query = $this->db->prepare($this->sql);
            $this->query->execute(array($id));
        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    function getByID($id)
    {
        $this->sql = "SELECT * FROM {$this->table} WHERE id = ?";

        try {
            $this->query = $this->db->prepare($this->sql);
            $this->query->execute(array($id));
            $this->results = $this->query->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            return $e->getMessage();
        }

        return $this->results;
    }
}
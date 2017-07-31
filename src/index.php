<?php

require_once('../vendor/autoload.php');

class Vivid {
    protected $db;
    protected $query,$table,$results,$limit;

    function __construct($host,$database_name,$username,$password)
    {
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$database_name",$username,$password);
        } catch(PDOException $e) {
            echo $e->getMessage();
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
            $sql = "SELECT * FROM $this->table LIMIT $this->limit";
        } else {
            $sql = "SELECT * FROM $this->table";
        }
        try {
            $this->query = $this->db->prepare($sql);
            $this->query->execute();
            $this->results = $this->query->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
        var_dump($this->results);
    }
}

$vivid = new Vivid('localhost','phonebook','root','password');
$vivid->table('contact')
      ->get();
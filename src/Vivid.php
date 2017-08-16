<?php

class Vivid {
    protected $db;
    protected $host;
    protected $username;
    protected $password;
    protected $database;
    protected $query;
    protected $table;
    protected $results;
    protected $parameters = [];

    public function __construct($host = null, $username = null, $password = null, $database = null)
    {
        $this->host = $host ?? $_ENV['DB_HOST'];
        $this->username = $username ?? $_ENV['DB_USER'];
        $this->password = $password ?? $_ENV['DB_PASS'];
        $this->database = $database ?? $_ENV['DB_NAME'];

        $this->make();
    }

    public function make()
    {
        $this->db = new PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $this->host, $this->database),
            $this->username,
            $this->password
        );
    }
    /**
     * Select your desired table
     * @param  string $table
     * @return object       for chaining purpose
     */
    function table($table)
    {
        $this->table = $table;
        return $this;
    }
    /**
     * Displays all records from the database
     * @param  integer $limit
     * @return object
     */
    function all($limit = 20)
    {
        if(!$this->table) {
            throw new \Exception('You must set the table first.');
        }
        try {
            $query = $this
                    ->db
                    ->prepare("SELECT * FROM {$this->table} LIMIT :limit");

            $query->bindParam(':limit', $limit, PDO::PARAM_INT);

            $query->execute();
        } catch(PDOException $ex) {
            var_dump($ex->getMessage());
        }

        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    /**
     * Limits the results by specific number.
     * @param  integer $limit
     * @return object       for chaining purpose
     */
    public function limit($limit = 10)
    {
        $this->query .= "LIMIT :limit ";

        $this->addParameter(':limit', $limit, PDO::PARAM_INT);

        return $this;
    }
    /**
     * Make an array of parameters
     * @param string $parameter [description]
     * @param value $value     [description]
     * @param attribute $attribute
     */
    public function addParameter($parameter, $value, $attribute = null)
    {
        $this->parameters[$parameter] = [
            'value' => $value,
            'attribute' => $attribute
        ];
    }
    /**
     * Used to filter results based on a given conditions
     * @param  string $column [description]
     * @param  string $value  [description]
     * @return object       for chaining purpose
     */
    public function where($column, $value)
    {
        $this->query .= "WHERE {$column} = :value ";

        $this->addParameter(':value', $value, PDO::PARAM_STR);

        return $this;
    }

    public function andWhere($column, $value)
    {
        $this->query .= "AND {$column} = :value ";
        $this->addParameter(':value', $value, PDO::PARAM_STR);

        return $this;
    }
    /**
     * Binds parameters and executes the query.
     * @return [type] [description]
     */
    public function get()
    {
        try {
            $query = $this
                ->db
                ->prepare("SELECT * FROM {$this->table} {$this->query}");

            foreach($this->parameters as $key => $parameter) {
                $query->bindParam(
                    $key,
                    $parameter['value'],
                    $parameter['attribute']
                );
            }

            $query->execute();
        } catch(PDOException $ex) {
            vdump($ex->getMessage());
        }

        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    /**
     * Inserting records
     * @param  array  $columns
     * @return string       IF: There is an error
     */
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
    /**
     * Delete specific record
     * @param  string $table
     * @param  int    $id
     * @return string       IF: There is an error
     */
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
    /**
     * Update specific record
     * @param  array  $columns [description]
     * @param  int    $id      [description]
     * @return string       IF: There is an error
     */
    function update($columns = [], $id)
    {
        $set = '';
        $comma = 1;

        foreach($columns as $key => $value) {
            $set .= "{$key} = '$value'";
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
}
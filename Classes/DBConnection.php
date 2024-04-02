<?php

namespace Classes;

use PDO;
use PDOException;

class DBConnection
{
    private $connection;
    protected $sessionsTable = 'ussd_session';
    protected $ticketsTable = 'tickets';

    public function __construct(private string $host, private string $username, private string $password, private string $database)
    { }

    public function connect()
    {
        try {
            // Create a mysqli connection
            $this->connection = new \mysqli($this->host, $this->username, $this->password, $this->database);

            // Check connection
            if ($this->connection->connect_error) {
                die("Connection failed: " . $this->connection->connect_error);
            }

            return $this->connection;
            
            // // Create a new PDO instance
            // $dsn = "mysql:host={$this->host};dbname={$this->database}";
            // $this->connection = new PDO($dsn, $this->username, $this->password);

            // // Set PDO error mode to exception
            // $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "DB connected success";
            
            // // Return the connection
            // return $this->connection;
        } catch (PDOException $e) {
            // Handle connection errors
            die("Connection to DB server has failed: " . $e->getMessage());
        }
    }

    /**
     * Run a query
     */
    public function query($sql, $params = [])
    {
        try {
            // Prepare and execute the SQL query
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            // Return the result set
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle query errors
            die("Query failed: " . $e->getMessage());
        }
    }

    public function runInsertQuery($tableName, $data) :bool
    {
        switch($tableName){
            case 'sessions':
                $tableName = $this->sessionsTable;
                break;
            case 'tickets':
                $tableName = $this->ticketsTable;
                break;
            default:
                $tableName = 'unkown';
                break;

        }
            
        // Build the INSERT query
        $fields = implode(', ', array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $query = "INSERT INTO $tableName ($fields) VALUES ($values)";

        // Execute the INSERT query
        $result = mysqli_query($this->connection, $query);

        // Check if the query was successful
        if (!$result) {
            // If query failed, return false
            return false;
        }

        // Return true to indicate success
        return true;
    }

    public function runSelectQuery($tableName, $filters = []) :array
    {
        switch($tableName){
            case 'sessions':
                $tableName = $this->sessionsTable;
                break;
            case 'tickets':
                $tableName = $this->ticketsTable;
                break;
            default:
                $tableName = 'unkown';
                break;

        }
            
        // Build the WHERE clause based on filter criteria
        $where = '';
        if (!empty($filters)) {
            $where = ' WHERE ';
            $conditions = [];
            foreach ($filters as $column => $value) {
                $conditions[] = "$column = '$value'";
            }
            $where .= implode(' AND ', $conditions);
        }

        // Build the SELECT query
        $query = "SELECT * FROM $tableName $where";

        // Execute the SELECT query
        $result = mysqli_query($this->connection, $query);

        // Check if the query was successful
        if (!$result) {
            // If query failed, return false
            return false;
        }

        // Fetch all rows from the result set
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free result set
        mysqli_free_result($result);

        // Return the fetched rows
        return $rows;
    }

    
    public function runUpdateQuery($tableName, $updateData = [], $filters = [])
    {
        switch($tableName){
            case 'sessions':
                $tableName = $this->sessionsTable;
                break;
            case 'tickets':
                $tableName = $this->ticketsTable;
                break;
            default:
                $tableName = 'unkown';
                break;

        }
            
        // Build the SET clause for the UPDATE query
        $setValues = [];
        foreach ($updateData as $column => $value) {
            $setValues[] = "$column = '$value'";
        }
        $setClause = implode(', ', $setValues);

        // Build the WHERE clause based on filter criteria
        $where = '';
        if (!empty($filters)) {
            $where = ' WHERE ';
            $conditions = [];
            foreach ($filters as $column => $value) {
                $conditions[] = "$column = '$value'";
            }
            $where .= implode(' AND ', $conditions);
        }

        // Build the UPDATE query
        $query = "UPDATE $tableName SET $setClause $where";

        // Execute the UPDATE query
        $result = mysqli_query($this->connection, $query);

        // Check if the query was successful
        if (!$result) {
            // If query failed, return false
            return false;
        }

        // Return the number of affected rows
        return mysqli_affected_rows($this->connection);
    }
    /**
     * close the db connection
     */
    public function close() :bool
    {
        // Close the database connection
        return mysqli_close($this->connection);
    }
    
    // Add more methods for performing database actions as needed
}

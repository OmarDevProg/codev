<?php



class Dbf
{
    private $dbn = "codevfordb14"; // Database name
    private $lhost = "localhost"; // OVH server hostname
    private $username = "root"; // Database username
    private $password = ""; // Database password

    public $conF;

    public function __construct()
    {
        try {
            $option = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
            $dsn = "mysql:host={$this->lhost};dbname={$this->dbn};charset=utf8";
            $this->conF = new PDO($dsn, $this->username, $this->password, $option);
            // Uncomment the line below for debugging purposes
            // echo "Connected successfully";
        } catch (PDOException $e) {
            // Logging the error or handling it gracefully
            // Consider using error_log() instead of echoing the message
            error_log("Connection failed: " . $e->getMessage());
            die("Sorry, you can't connect to the database right now. Please try again later.");
        }
    }
    public function select($query, $params = [])
    {
        try {
            $stmt = $this->conF->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log and return error message
            error_log("Query failed: " . $e->getMessage());
            return false;
        }
    }

    // Insert method
    public function insert($query, $params)
    {
        try {
            $stmt = $this->conF->prepare($query);
            $stmt->execute($params);
            return $this->conF->lastInsertId(); // Returns the last inserted ID
        } catch (PDOException $e) {
            // Log and return error message
            error_log("Insert failed: " . $e->getMessage());
            echo "Error: " . $e->getMessage(); // Display error to the user for debugging
            return false;
        }
    }


    // Update method
    public function update($query, $params)
    {
        try {
            // Debug: Log the query and params
            error_log("Executing query: " . $query);
            error_log("With parameters: " . print_r($params, true));

            $stmt = $this->conF->prepare($query);
            $stmt->execute($params);

            // Check number of affected rows
            $rowCount = $stmt->rowCount();
            if ($rowCount === 0) {
                error_log("No rows were updated. Query might not have matched any rows.");
            }
            return $rowCount;
        } catch (PDOException $e) {
            // Log and return error message
            error_log("Update failed: " . $e->getMessage());
            return false;
        }
    }


    // Delete method
    public function delete($query, $params)
    {
        try {
            $stmt = $this->conF->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount(); // Returns the number of affected rows
        } catch (PDOException $e) {
            // Log and return error message
            error_log("Delete failed: " . $e->getMessage());
            return false;
        }
    }
}

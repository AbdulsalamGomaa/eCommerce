<?php 

class config {

    private $hostname = "localhost";
    private $username = "root";
    private $password = "";
    private $conn;
    public function __construct() {

        try {
            $this->conn = new PDO("mysql:host=$this->hostname;dbname=nti_ecommerce",$this->username,$this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connected successfully";

        } catch (PDOException $e) {
            
            // echo "Connected Failed" . $e->getMessage();
        }
    }

    // insert - update - delete
    public function runDML(string $query) : bool {
        
        $result = $this->conn->query($query);
        if($result) {

            return true;
        }else {

            return false;
        }
    }

    // select
    public function runDQL(string $query) : array|object {
        
        $result = $this->conn->query($query);
        if($result->rowCount() > 0) {
            return $result;
        }else {
            return [];
        }
    }
}

// $connection = new config;


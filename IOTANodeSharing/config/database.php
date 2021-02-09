<?php
class Database{
  
    // specify your own database credentials
    private $host = "mysql5.wisewolf.de";
    private $db_name = "db138028_4";
    private $username = "admin_138028";
    private $password = "6zsht4Go!";
    public $conn;
  
    // get the database connection
    public function getConnection(){
  
        $this->conn = null;
  
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
}
?>
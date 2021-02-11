<?php
class Peers{
  
    // database connection and table name
    private $conn;
    private $table_name = "Peers";
  
    // object properties
    public $id;
    public $peerAdress;
    public $peerID;
    public $port;
    public $apiPort;
    public $eMail;
    public $availability;
    public $dateAdded;
    public $lastAvailable;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read peers
    function read($originalNode, $originalPort){
    
        // select all query
        $query = "SELECT p1.PeerAdress, p1.Port, p1.APIPort, p1.PeerID, p1.eMail, COUNT(*)
                    FROM " . $this->table_name . " p1
                    LEFT JOIN PeeringStatus s1 ON s1.PeersID = p1.ID
                    LEFT JOIN PeeringStatus s2 ON s2.MatchedPeersID = p1.ID
                    WHERE p1.Availability = 1
                    GROUP BY p1.peerAdress, p1.Port, p1.APIPort, p1.PeerID, p1.eMail, p1.DateAdded
                    ORDER BY COUNT(*) ASC, p1.DateAdded ASC
                    LIMIT 3";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    // create peer
    function create(){
    
        // query to insert record
        $query = "INSERT INTO
                    " . $this->table_name . "
                SET
                    PeerAdress=:peerAdress, Port=:port, APIPort=:apiPort, PeerID=:peerID, eMail=:eMail, DateAdded=:dateAdded, Availability=:availability, LastAvailable=:lastAvailable";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        $this->dateAdded=htmlspecialchars(strip_tags($this->dateAdded));
        $this->lastAvailable=htmlspecialchars(strip_tags($this->lastAvailable));
    
        // bind values
        $stmt->bindParam(":peerAdress", $this->peerAdress);
        $stmt->bindParam(":port", $this->port);
        $stmt->bindParam(":apiPort", $this->apiPort);
        $stmt->bindParam(":peerID", $this->peerID);
        $stmt->bindParam(":eMail", $this->eMail);
        $stmt->bindParam(":availability", $this->availability);
        $stmt->bindParam(":dateAdded", $this->dateAdded);
        $stmt->bindParam(":lastAvailable", $this->lastAvailable);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
        
    }
}
?>
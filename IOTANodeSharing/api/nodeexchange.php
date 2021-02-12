<?php
    // required headers
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    // include database and class files
    include '../config/database.php';
    include 'class_peers.php';
    include '../config/class_encryption.php';
    include 'cryptservice.php';
    
    // instantiate database and peers object
    $database = new Database();
    $db = $database->getConnection();
    
    // initialize object
    $peers = new Peers($db);

    // query peers
    $stmt = $peers->read("test", "test");
    $num = $stmt->rowCount();
    
    // check if more than 0 record found
    if($num>0){
    
        // peers array
        $peers_arr=array();
        $peers_arr["records"]=array();
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $peers_item = "";

            // extract row
            // this will make $row['name'] to
            // just $name only
            extract($row);
            
            // when the given adress is not an IP-Adress but a DNS, the long ID for peering slightly differs
            if(filter_var($PeerAdress, FILTER_VALIDATE_IP)) {
                $peers_item = "/ip4/";
            } else {
                $peers_item = "/dns/";
            }

            $peers_item .= decryptify($PeerAdress) . "/tcp/" . decryptify($Port) . "/p2p/" . decryptify($PeerID);
            $peers_item_array = array("peerID" => $peers_item, "eMail" => decryptify($eMail));
    
            array_push($peers_arr["records"], $peers_item_array);
        }
    
        // set response code - 200 OK
        http_response_code(200);
    
        // show peers data in json format
        echo json_encode($peers_arr);
    } else { 
  
        // set response code - 404 Not found
        http_response_code(404);
      
        // tell the user no peers found
        echo json_encode(
            array("message" => "No peers found.")
        );
    }
?>
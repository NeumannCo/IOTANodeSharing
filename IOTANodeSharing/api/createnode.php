<?php
    // required headers
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // get database connection
    include '../config/database.php';
    
    // instantiate peers object
    include 'class_peers.php';
    include '../config/class_encryption.php';
    include 'healthcheck.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $peers = new Peers($db);
    $encryption = new Encryption();
    
    // get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // make sure data is not empty
    if(
        !empty($data->peerAdress) &&
        !empty($data->port) &&
        !empty($data->apiPort) &&
        !empty($data->peerID)
    ){
        if(nodeHealthCheck($data->peerAdress, $data->apiPort) == 200) {
        
            // set peers property values
            $peers->peerAdress = $encryption->cryptify($data->peerAdress);
            $peers->port = $encryption->cryptify($data->port);
            $peers->apiPort = $encryption->cryptify($data->apiPort);
            $peers->peerID = $encryption->cryptify($data->peerID);
            if(!empty($data->eMail)){
                $peers->eMail = $encryption->cryptify($data->eMail);
            } else {
                $peers->eMail = $encryption->cryptify("");
            }
            $peers->dateAdded = date('Y-m-d H:i:s');
            $peers->availability = 1;
            $peers->lastAvailable = date('Y-m-d H:i:s');
        
            // create the product
            if($peers->create()){
        
                // set response code - 201 created
                http_response_code(201);
        
                // tell the user
                echo json_encode(array("message" => "Peer was created."));
            }
        
            // if unable to create the product, tell the user
            else{
        
                // set response code - 503 service unavailable
                http_response_code(503);
        
                // tell the user
                echo json_encode(array("message" => "Unable to create peer."));
            }
        } else {
            // set response code - 503 service unavailable
            http_response_code(503);
        
            // tell the user
            echo json_encode(array("message" => "Unable to create peer. Node failed the health check."));
        }
    }
    
    // tell the user data is incomplete
    else{
    
        // set response code - 400 bad request
        http_response_code(400);
    
        // tell the user
        echo json_encode(array("message" => "Unable to create peer. Data is incomplete."));
    }

?>
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
    
    $database = new Database();
    $db = $database->getConnection();
    
    $peers = new Peers($db);

    // query peers
    $checkstmt = $peers->readAll(1);
        
    $encryption = new Encryption();

    while ($row = $checkstmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        
        $peerToCheck = new Peers($db);
        $peerToCheck->peerAdress = $encryption->decryptify($PeerAdress);
        $peerToCheck->apiPort = $encryption->decryptify($APIPort);
        $peerToCheck->port = $encryption->decryptify($Port);

        if($peerToCheck->healthCheck() == 200 || $peerToCheck->healthCheck() == 503 || $peerToCheck->healthCheck() == 401) {
        
            $peerToCheck->updateAvailability();
            echo $ID . " is healthy.\r\n";

        } else {
            $peerToCheck->disable();
            echo $ID . " is unhealthy and was disabled.\r\n";
        }
    }

    echo "Health-Check successfully completed.";

?>
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
    $stmt = $peers->readAll(1);
        
    $encryption = new Encryption();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $peers_item = "";

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
            echo "Is healthy.\r\n";

        } else {
            $peersHealthy = false;
            $peerToCheck->disable();
            echo "Is unhealthy and was disabled.\r\n";
        }
    }

    echo "Health-Check successfully completed.";

?>
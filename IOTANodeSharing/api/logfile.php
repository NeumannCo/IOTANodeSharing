<?php

    // get database connection
    include '../config/database.php';
    include '../config/class_encryption.php';

    if(hash('sha256', $_GET['pw']) == '91b4f37980fb5c3305577b09061f7d1e86aae35ea8ab0f4c1d8efda9c934b354') {
        $database = new Database();
        $db = $database->getConnection();

        $encryption = new Encryption();

        // select all query
        $query = "SELECT ID, RequestTime, PeerAdress, Port, APIPort, PeerID, Network FROM RequestLog ORDER BY ID DESC LIMIT 5000" ;

        // prepare query statement
        $stmt = $db->prepare($query);

        // execute query
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            echo $ID . "; " . $RequestTime . "; ";

            if(filter_var($encryption->decryptify($PeerAdress), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                echo "ip4;";
            } elseif(filter_var($encryption->decryptify($PeerAdress), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                echo "ip6;";
            } else {
                echo "dns;";
            }

            echo $encryption->decryptify($PeerAdress) . "; " . $encryption->decryptify($Port) . "; " . $encryption->decryptify($APIPort) . "; " . $encryption->decryptify($PeerID) . "; " . $Network . "<br />\r\n";
        }
    } else {
        echo 'Authentication failed.';
    }
?>
<?php
    include "api/class_peers.php";
    include "config/class_encryption.php";
    include "config/database.php";

    $encryption = new Encryption();

    $adress = "hornet.wisewolf.de";
    $adressEnc = $encryption->cryptify($adress);

    $database = new Database();
    $db = $database->getConnection();

    $node1 = new Peers($db);
    $node2 = new Peers($db);
    $node3 = new Peers($db);

    $node1->peerAdress = "hornet.wisewolf.de";
    $node1->apiPort = 5000;
    $node2->peerAdress = "hornet2.wisewolf.de";
    $node2->apiPort = 5000;
    $node3->peerAdress = "hornet3.wisewolf.de";
    $node3->apiPort = 5000;

?>


<html>
    <head></head>
    <body>
    <?php
    echo $adressEnc;
    echo "<br>";
    echo "<br>";
    echo $encryption->decryptify($adressEnc);

    echo "<br>hornet1 " . $node1->healthCheck();
    echo "<br>hornet2 " . $node2->healthCheck();
    echo "<br>hornet3 " . $node3->healthCheck();
    
    ?>
    </body>

</html>
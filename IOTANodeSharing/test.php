<?php
    include "api/cryptservice.php";
    include "api/healthcheck.php";
    include "config/class_encryption.php";

    $adress = "hornet.wisewolf.de";
    $adressEnc = cryptify($adress);

    echo $adressEnc;
    echo "\n";
    echo "\n";
    echo decryptify($adressEnc);

    echo nodeHealthCheck("hornet2.wisewolf.de", "5000")
?>
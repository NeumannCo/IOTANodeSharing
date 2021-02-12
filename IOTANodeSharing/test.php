<?php
    include "api/healthcheck.php";
    include "config/class_encryption.php";

    $encryption = new Encryption();

    $adress = "hornet.wisewolf.de";
    $adressEnc = $encryption->cryptify($adress);

    echo $adressEnc;
    echo "\n";
    echo "\n";
    echo $encryption->decryptify($adressEnc);

    echo nodeHealthCheck("hornet2.wisewolf.de", "5000")
?>
<?php
    include "api/cryptservice.php";

    $adress = "hornet.wisewolf.de";
    $adressEnc = cryptify($adress);

    echo $adressEnc;
    echo "\n";
    echo "\n";
    echo decryptify($adressEnc);
?>
<?php

    function sendmail($eMail, $matchedID) {
        $empfaenger = $eMail;
        $betreff = "Your Hornet/Bee - Peer ID was presented to another Node Owner";
        $nachricht = "Hi," . "\r\n\r\n" . "you added your Peer ID for Nodesharing. Your Peer ID was recently matched with the following ID:" . "\r\n\r\n" . $matchedID . "\r\n\r\n" . "Please consider to add this ID to your Node as well." . "\r\n\r\n" . "Thank You!";
        $header = 'From: no-reply@wisewolf.de' . "\r\n" .
            'Reply-To: no-reply@wisewolf.de' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($empfaenger, $betreff, $nachricht, $header);
    }
?>
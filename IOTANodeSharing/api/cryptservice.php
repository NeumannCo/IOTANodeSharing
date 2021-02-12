<?php

    // IMPORTANT: Whenever this file is included, encryption.php has to be included as well!

    function cryptify($valueToEncrypt) {
        $cr = new Encryption();
        $output = false;
    
        // hash
        $key = hash('sha256', $cr->getCryptKey());
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $cr->getCryptIv()), 0, 16);
    
        $output = openssl_encrypt($valueToEncrypt, $cr->getEncryptionMethod(), $key, 0, $iv);
        $output = base64_encode($output);
    
        return $output;
    }

    function decryptify($valueToDecrypt) {
        $cr = new Encryption();
        $output = false;

        // hash
        $key = hash('sha256', $cr->getCryptKey());
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $cr->getCryptIv()), 0, 16);

        $output = openssl_decrypt(base64_decode($valueToDecrypt), $cr->getEncryptionMethod(), $key, 0, $iv);
    
        return $output;
    }

?>
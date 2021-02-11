<?php

    // IMPORTANT: Whenever this file is included, encryption.php has to be included as well!

    function cryptify($valueToEncrypt) {
        $output = false;
    
        // hash
        $key = hash('sha256', getCryptKey());
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', getCryptIv()), 0, 16);
    
        $output = openssl_encrypt($valueToEncrypt, getEncryptionMethod(), $key, 0, $iv);
        $output = base64_encode($output);
    
        return $output;
    }

    function decryptify($valueToDecrypt) {
        $output = false;

        // hash
        $key = hash('sha256', getCryptKey());
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', getCryptIv()), 0, 16);

        $output = openssl_decrypt(base64_decode($valueToDecrypt), getEncryptionMethod(), $key, 0, $iv);
    
        return $output;
    }

?>
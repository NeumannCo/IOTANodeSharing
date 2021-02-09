<?php

    function cryptify($valueToEncrypt) {
        $key = "1otaR0ckz";
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_iv = 'myS3cr3t1v';
    
        // hash
        $key = hash('sha256', $key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
    
        $output = openssl_encrypt($valueToEncrypt, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    
        return $output;
    }

    function decryptify($valueToDecrypt) {
        $key = "1otaR0ckz";
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_iv = 'myS3cr3t1v';
    
        // hash
        $key = hash('sha256', $key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = openssl_decrypt(base64_decode($valueToDecrypt), $encrypt_method, $key, 0, $iv);
    
        return $output;
    }

?>
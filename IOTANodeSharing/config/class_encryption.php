<?php

    class Encryption {

        private $cryptKey = "Your Key here";
        private $cryptIv = "Your IV here";
        private $encryptionMethod = "AES-256-CBC";

        function cryptify($valueToEncrypt) {
            $output = false;
        
            // hash
            $key = hash('sha256', $this->cryptKey);
            
            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $this->cryptIv), 0, 16);
        
            $output = openssl_encrypt($valueToEncrypt, $this->encryptionMethod, $key, 0, $iv);
            $output = base64_encode($output);
        
            return $output;
        }
    
        function decryptify($valueToDecrypt) {
            $output = false;
    
            // hash
            $key = hash('sha256', $this->cryptKey);
            
            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $this->cryptIv), 0, 16);
    
            $output = openssl_decrypt(base64_decode($valueToDecrypt), $this->encryptionMethod, $key, 0, $iv);
        
            return $output;
        }

    }

?>
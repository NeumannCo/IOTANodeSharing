<?php

    // IMPORTANT: Whenever cryptservice.php is included, this file has to be included as well!

    class Encryption {

        private $cryptKey = "your key here";
        private $cryptIv = "your iv here";
        private $encryptionMethod = "AES-256-CBC";

        function getCryptKey(){
            // define the Encryption Key that is used globally
            return $this->cryptKey;
        }
        
        function getCryptIv(){
            // define the Encryption Initialisation Vector that is used globally
            return $this->cryptIv;
        }

        function getEncryptionMethod(){
            // define the Encryption Method that is used globally
            return $this->encryptionMethod;
        }
    }

?>
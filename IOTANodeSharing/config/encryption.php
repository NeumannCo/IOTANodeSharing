<?php

    // IMPORTANT: Whenever cryptservice.php is included, this file has to be included as well!

    function getCryptKey(){
        // define the Encryption Key that is used globally
        return "your key here";
    }
    
    function getCryptIv(){
        // define the Encryption Initialisation Vector that is used globally
        return "your IV here";
    }

    function getEncryptionMethod(){
        // define the Encryption Method that is used globally
        return "AES-256-CBC";
    }
?>
<?php

    function nodeHealthCheck($checkAdress, $checkApiPort) {
        $url = $checkAdress . ':' . $checkApiPort . '/health';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $httpcode;
    }
       
?>
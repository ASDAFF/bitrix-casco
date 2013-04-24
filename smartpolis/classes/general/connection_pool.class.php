<?php
/**
 * Модуль Умный Полис
 *
 * @file classes/general/connection_pool.class.php
 */

class SmartpolisConnectionPool {
    private $apiPoint = "http://casco.cmios.ru/rest/default";

    public function get($url) {
        return file_get_contents($this->apiPoint . $url);
    }

    public function post($url, $data, $debug = 0) {
        $ch = curl_init( $this->apiPoint . $url );
        curl_setopt($ch, CURLOPT_HEADER, $debug);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}

?>
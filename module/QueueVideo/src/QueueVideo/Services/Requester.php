<?php

namespace QueueVideo\Services;

abstract class Requester {
    protected $url;
    protected $key;
    
    public function __construct($url, $key) {
        $this->setKey($key);
        $this->setUrl($url);
    }
    
    public function getUrl() {
        return $this->url;
    }

    public function getKey() {
        return $this->key;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function setKey($key) {
        $this->key = $key;
    }
    
    protected function request($url) {
        echo "Requesting: " . $url . "\n";
        return file_get_contents($url);
    }

}
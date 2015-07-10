<?php

namespace QueueVideo\Services;

class JSONRequester extends Requester implements RequesterInterface{
    public function query($command) {
        $returnBody = $this->request($this->getUrl() . $command . "&key=" . $this->getKey());
        return json_decode($returnBody, true);
    }
}
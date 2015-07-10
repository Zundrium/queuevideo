<?php

namespace QueueVideo\Services;

use QueueVideo\Models\MediaItem\MediaItem;

abstract class MediaFinder {
    protected $requester;
    protected $cache;
    
    public function __construct($requester) {
        $this->setRequester($requester);
    }
    
    public function getRequester() {
        return $this->requester;
    }

    public function setRequester($requester) {
        $this->requester = $requester;
    }
    
    protected function find($url, $parameters) {
        return $this->requester->query($url . $this->generateGetterString($parameters));
    }
    
    private function generateGetterString($parameters) {
        $string = "?";
        foreach($parameters as $name => $parameter) {
            $string .= $name . '=' . urlencode($parameter) . "&";
        }
        return substr($string, 0, -1);
    }

    public function parseMediaItemsFromResponse($response) {
        $returnArray = array();
        foreach($response as $mediaItemInfo) {
            $mediaItem = $this->parseMediaItemFromResponse($mediaItemInfo);
            $returnArray[] = $mediaItem;
        }
        return $returnArray;
    }
    
    public function isInCache($mediaItemId) {
        return isset($this->cache[$mediaItemId]);
    }
    
    public function getMediaItemFromCache($mediaItemId) {
        return $this->cache[$mediaItemId];
    }
    
    public function addToCache(MediaItem $mediaItem) {
        $this->cache[$mediaItem->getLink()] = $mediaItem;
    }

}
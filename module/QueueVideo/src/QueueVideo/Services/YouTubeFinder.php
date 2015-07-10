<?php

namespace QueueVideo\Services;
use QueueVideo\Models\MediaItem\MediaItem;

class YouTubeFinder extends MediaFinder implements MediaFinderInterface {
    public function getMediaItemById($mediaItemId) {
        echo "Searching for youtube video (youtube video link:" . $mediaItemId . ") information.\n";
        if($this->isInCache($mediaItemId)) {
            $mediaItem = $this->getMediaItemFromCache($mediaItemId);
        } else {
            $listResponse = $this->find(
                "videos", 
                array(
                    "part"            => "id,snippet,contentDetails",
                    "id"      => $mediaItemId
                )
            );
            $mediaItem = $this->parseMediaItemFromResponse($listResponse["items"][0]);
            $this->addToCache($mediaItem);
        }
        return $mediaItem;
    }

    public function getMediaItemsByPlaylistId($playlistId) {
        $playlistItems = $this->find(
            "playlistItems", 
            array(
                "part"            => "contentDetails",
                "playlistId"      => $playlistId,
                "maxResults"      => 50
            )
        );
        $idString = $this->createIdStringFromPlaylistItems($playlistItems["items"]);
        $response = $this->find(
            "videos", 
            array(
                "part"            => "id,snippet,contentDetails",
                "id"              => $idString
            )
        );
        $mediaItems = $this->parseMediaItemsFromResponse($response["items"]);
        foreach($mediaItems as $mediaItem) {
            $this->addToCache($mediaItem);
        }
        return $mediaItems;
    }
    
    public function createIdStringFromPlaylistItems($playlistItems) {
        $idString = "";
        foreach($playlistItems as $playlistItem) {
            $idString .= $playlistItem["contentDetails"]["videoId"] . ",";
        }
        return substr($idString, 0, -1);
    }
    
    public function createIdStringFromPlaylists($playlists) {
        $idString = "";
        foreach($playlists as $playlist) {
            $idString .= $playlist["id"]["playlistId"] . ",";
        }
        return substr($idString, 0, -1);
    }

    public function parseMediaItemFromResponse($singleResponse) {
        $mediaItem = new MediaItem();
        if(isset($singleResponse["id"])) {
            $mediaItem->setLink($singleResponse["id"]);
        } else {
            $mediaItem->setLink($singleResponse["id"]["videoId"]);
        }
        $mediaItem->setTitle($singleResponse["snippet"]["title"]);
        $mediaItem->setChannelId($singleResponse["snippet"]["channelId"]);
        $mediaItem->setChannelTitle($singleResponse["snippet"]["channelTitle"]);
        $mediaItem->setThumbnail($singleResponse["snippet"]["thumbnails"]["default"]["url"]);
        $mediaItem->setDescription($singleResponse["snippet"]["description"]);
        if(isset($singleResponse["contentDetails"])) {
            $mediaItem->setDuration($this->youtubeDurationToTimestamp($singleResponse["contentDetails"]["duration"]));
        } else {
            $mediaItem->setDuration(0);
        }
        return $mediaItem;
    }

    public function parsePlaylistFromResponse($item) {
        return array(
            "id" => $item["id"]["playlistId"],
            "thumbnail" => $item["snippet"]["thumbnails"]["default"],
            "name" => $item["snippet"]["title"],
            "description" => $item["snippet"]["description"],
            "channelTitle" => $item["snippet"]["channelTitle"],
            "channelId" => $item["snippet"]["channelId"], 
            "published" => $item["snippet"]["publishedAt"],
            "mediaItemAmount" => $item['data']['contentDetails']['itemCount']
        );
    }

    public function searchForMediaItems($searchString, $limit) {
        $searchResponse = $this->find(
            "search", 
            array(
                "part"            => "id,snippet",
                "q"               => $searchString,
                "maxResults"      => $limit,
                "type"            => "video",
                "videoEmbeddable" => "true"
            )
        );
        $returnArray = array();
        foreach ($searchResponse["items"] as $searchResult) {
            $returnArray[] = $this->parseMediaItemFromResponse($searchResult);
        }   
        return $returnArray;
    }
    
    
    public function searchForPlaylists($searchString, $limit) {        
        $searchResponse = $this->find(
            "search", 
            array(
                "part"            => "id,snippet",
                "q"               => $searchString,
                "maxResults"      => $limit,
                "type"            => "playlist"
            )
        );
        $idString = $this->createIdStringFromPlaylists($searchResponse["items"]);
        $contentDetails = $this->find(
            "playlists", 
            array(
                "part"            => "contentDetails",
                "id"               => $idString
            )
        ); 
        $returnArray = array();
        for ($playlistCounter=0; $playlistCounter < count($searchResponse["items"]); $playlistCounter++) { 
            $searchResponse["items"][$playlistCounter]['data']['contentDetails'] = $contentDetails["items"][$playlistCounter]["contentDetails"];
            $returnArray[] = $this->parsePlaylistFromResponse($searchResponse["items"][$playlistCounter]);
        }
        var_dump($returnArray);
        return $returnArray;
    }
    
    public function youtubeDurationToTimestamp($youtubeDuration) {
        $tempDuration = substr($youtubeDuration, 2, -1);
        $seconds = 0;
        if (strlen($tempDuration) > 5) {
            $hours = explode("H", $tempDuration);
            $seconds = intval($hours[0]) * 60 * 60;
            $tempDuration = $hours[1];
        } 
        if (strlen($tempDuration) > 2) {
            $minutes = explode("M", $tempDuration);
            $seconds += intval($minutes[0]) * 60;
            $tempDuration = $minutes[1];
        } 
        $seconds += intval($tempDuration);
        echo 'Converted ' . $youtubeDuration . " to " . $seconds . ".\n";
        return $seconds;
    }
}

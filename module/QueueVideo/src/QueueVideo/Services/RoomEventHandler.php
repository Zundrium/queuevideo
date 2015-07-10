<?php
namespace QueueVideo\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;
use Exception;
use Zend\Db\Adapter\Adapter;
use ZfcUser\Entity\User;
use QueueVideo\Models\MediaItem\MediaItem;
use QueueVideo\Services\YouTubeFinder;

class RoomEventHandler implements MessageComponentInterface {
    protected $clients;
    private $whiteList;
    private $queue;
    private $mediaItemStartingTimestamp;
    private $currentMediaItem;
    private $adapter;

    public function __construct() {
        $this->setDefaultValues();
        $this->openConnection();
        echo "Room Event Handler Started\n";
    }

    public function setDefaultValues() {
        $this->clients = new SplObjectStorage;
        $this->initializeYoutubeFinder();
        $this->queue = array();
        $this->whiteList = array(
            "addMessage" => array(
                "to" => "everyone",
                "energyCost" => 1,
                "experienceGained" => 0
            ), 
            "requestNextMediaItem" => array(
                "to" => "everyone",
                "energyCost" => 0,
                "experienceGained" => 0
            ),
            "authenticate" => array(
                "to" => "everyone",
                "energyCost" => 0,
                "experienceGained" => 0
            ),
            "removeMediaItem" => array(
                "to" => "everyone",
                "energyCost" => 5,
                "experienceGained" => 0
            ),
            "searchForMediaItem" => array(
                "to" => "self",
                "energyCost" => 0,
                "experienceGained" => 0
            ),
            "searchForPlaylist" => array(
                "to" => "self",
                "energyCost" => 0,
                "experienceGained" => 0
            ),
            "addMediaItem" => array(
                "to" => "everyone",
                "energyCost" => 5,
                "experienceGained" => 10
            ),
            "addPlaylist" => array(
                "to" => "everyone",
                "energyCost" => 5,
                "experienceGained" => 10
            ), 
            "reloadMediaItem" => array(
                "to" => "self",
                "energyCost" => 0,
                "experienceGained" => 0
            )
        );
    }
    
    public function initializeYoutubeFinder() {
        $this->youtubeFinder = new YouTubeFinder(
            new JSONRequester(
                "https://www.googleapis.com/youtube/v3/",
                "AIzaSyD9x2l2L_wcXhrZNOOAIHoownZ4qvma360"
            )
        );
    }

    public function openConnection() {
        $this->adapter = new Adapter(array( 
            'driver' => 'Pdo_Mysql',
            'database' => 'queuevideo',
            'username' => 'sem',
            'password' => 'Celeronmsn123'
        ));
    }

    public function query($sql, $values, $retry = 0) {
        try {
            $result = $this->adapter->query($sql, $values);
        } catch (Exception $e) {
            if ($retry == 5) {
                throw $e;
            }
            echo "Reopening connection. (Retry: " . $retry . ")\n";
            $retry++;
            $this->openConnection(); 
            $result = $this->query($sql, $values, $retry);
        }
        return $result;
    }

    public function onOpen(ConnectionInterface $conn) {
        echo "New connection! (" . $conn->resourceId . ")\n";
    }

    public function authenticate($from, $incomingMessageArray) {
        echo "Authenticating user.\n";
        $userId = $this->getUserIdFromSessionId($incomingMessageArray["sessionid"]);
        if ($userId) {
            $user = $this->getUserById($userId);
        } else {
            $user = $this->getNewGuestUser($from->resourceId);
        }
        $from->user = $user;
        $from->energyTimestamp = microtime(true);
        $from->send(json_encode($this->getBasicRoomInformation()));
        $this->clients->attach($from);
        return array(
            'method' => 'newUser',
            'userName' => $from->user->getDisplayName(),
            "sessionId" => $from->resourceId
        );
    }

    public function userIsAlreadyInARoom(User $checkedUser){ 
        $bool = false;
        foreach ($this->clients as $client) {
            if($client->user->getId() == $checkedUser->getId()) {
                $bool = true;
                break;
            }
        }
        return $bool;
    }

    public function getBasicRoomInformation() {
        echo "Sending basic information.\n";
        $array = array(
            0 => array( 
                "method" => "loadUserList",
                "users" => $this->getUserList()
            ),
            1 => array(
                "method" => "loadQueue",
                "mediaItems" => $this->getQueueInfo()
            )
        );

        if($this->queueIsNotEmpty()) {
            $array[] = array(
                'method' => 'startMediaItem',
                'playFrom' => $this->howManySecondsAgoTheMediaItemStarted(),
                'mediaItem' => $this->convertMediaItemToArray($this->currentMediaItem)
            );
        }
        return $array;
    }

    public function reloadMediaItem($from, $incomingMessageArray) {
        if(!is_null($this->currentMediaItem)) { 
            return array(
                'method' => 'startMediaItem',
                'playFrom' => $this->howManySecondsAgoTheMediaItemStarted(),
                'mediaItem' => $this->convertMediaItemToArray($this->currentMediaItem)
            );
        }
    }

    public function getUserIdFromSessionId($id) {
        $result = $this->query(
            'SELECT * 
            FROM `session` 
            WHERE `id` = ?',
            array(
                $id
            )
        );
        $row = $result->current();
        $array = $this->unserialize_session_data($row["data"]);
        if(isset($array["Zend_Auth"])) { 
            return $array["Zend_Auth"]->storage;
        } else {
            return false;
        }
    }

    public function getUserById($id) {
        $result = $this->query(
            'SELECT * 
            FROM `user` 
            WHERE `user_id` = ?',
            array(
                $id
            )
        );
        $row = $result->current();
        $user = new User();
        $user->setUsername($row['username'])
                ->setEmail($row['email'])
                ->setDisplayName($row['display_name'])
                ->setPassword($row['password']);
        return $user;
    }

    public function getNewGuestUser($id) {
        $user = new User();
        $user->setUsername('guest_' . $id)
                ->setDisplayName('guest_' . $id);
        return $user;
    }

    public function unserialize_session_data( $serialized_string ) {
       $variables = array();
       $a = preg_split("/(\w+)\|/", $serialized_string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
       for($i=0;$i<count($a);$i=$i+2){
           $variables[$a[$i]] = unserialize($a[$i+1]);
       }
       return($variables);
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
            foreach ($this->clients as $client) {
                $client->send(
                    json_encode(
                        array(
            
                            "userName" => $conn->user->getDisplayName(),
                            "sessionId" => $conn->resourceId,
                            "method" => "userLeaves"
                        )
                    )
                );
            }
        echo "Connection {$conn->resourceId} has disconnected\n\n";
    }

     public function onMessage(ConnectionInterface $from, $msg) {
        $incomingMessageArray = json_decode($msg,true);
        if(array_key_exists($incomingMessageArray["method"], $this->whiteList) 
            && $this->userEnergy($from) >= $this->whiteList[$incomingMessageArray["method"]]["energyCost"]) {
            if($this->whiteList[$incomingMessageArray["method"]]["energyCost"] > 0) { 
                $this->updateEnergy($from, $this->userEnergy($from) - $this->whiteList[$incomingMessageArray["method"]]["energyCost"]);
            }
            echo "Executing method: " . $incomingMessageArray["method"] . "\n";
            $outgoingMessageArray = $this->{$incomingMessageArray["method"]}($from, $incomingMessageArray);
            switch($this->whiteList[$incomingMessageArray["method"]]["to"]) {
                case "everyone":
                    $this->sendToEveryone($outgoingMessageArray);
                break;
                case "self":
                    $from->send(json_encode($outgoingMessageArray));
                break;
            }
        }
    }

    public function sendToEveryone($array) {
        foreach ($this->clients as $client) {
            $client->send(json_encode($array));
        }
    }

    public function getFirstClient() {
        foreach ($this->clients as $client) {
            return $client;
	    break;
        } 
    }

    public function getUserList() {
        $array = array();
        foreach ($this->clients as $client) {
            $array[] = array(
                "sessionId" => $client->resourceId,
                "userName" => $client->user->getDisplayName(),
                "energy" => $this->userEnergy($client)
                );
        }
        return $array;
    } 

    public function addMessage($from, $incomingMessageArray) {
        echo $from->resourceId . " says \"" . $incomingMessageArray["message"] . "\".\n";
        $message = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is",
                    "\\1<a href=\"\\2\">\\2</a>", 
                    htmlentities($incomingMessageArray["message"], ENT_QUOTES));
    	if($incomingMessageArray["message"] != "") { 
            return array(
                    "method" => "addMessage",
                    "userName" => $from->user->getDisplayName(),
                    "message" => $message,
    
                );
        }
    }

    public function getQueueInfo() {
        $array = array();
        foreach ($this->queue as $mediaItem) {
            $array[] = $this->convertMediaItemToArray($mediaItem);
        }
        return $array;
    }

    public function startQueue() { 
        $this->startMediaItem(current($this->queue));
    }

    public function requestNextMediaItem() {
        if(count($this->queue) > 0 && !$this->currentMediaItemIsStillPlaying()) {
            if(!is_null($this->currentMediaItem)) {
                $this->removeMediaItemFromQueue($this->currentMediaItem);
            }
            if(count($this->queue) > 0) { 
                $this->startMediaItem($this->getNextMediaItemInQueue());
            }
        }
    }

    public function currentMediaItemIsStillPlaying() {
        if (is_null($this->currentMediaItem)) {
            return false;
        } else {
            var_dump($this->currentMediaItem->getDuration()); 
            var_dump($this->howManySecondsAgoTheMediaItemStarted() + 5);
            return $this->currentMediaItem->getDuration() > ($this->howManySecondsAgoTheMediaItemStarted() + 5);
        }
    }

    public function mediaItemIsInQueue(MediaItem $checkedMediaItem) {
        $bool = false;
        foreach ($this->queue as $mediaItem) {
            if($checkedMediaItem->getlink() == $mediaItem->getLink()) {
                $bool = true;
                break;
            }
        }
        return $bool;
    }

    public function getNextMediaItemInQueue() {
        $counter = 1;
        $nextMediaItem = null;
        foreach ($this->queue as $mediaItem) {
            if($counter == 1) {
                $nextMediaItem = $mediaItem;
                break;
            }
            $counter++;
        }
        return $nextMediaItem;
    }

    public function removeMediaItemFromQueue(MediaItem $mediaItem ) {
        unset($this->queue[$mediaItem->getLink()]);
        $outgoingMessageArray = array(
            'method' => 'removeMediaItemFromQueue',
            'mediaItem' => $this->convertMediaItemToArray($mediaItem)
        );
        foreach ($this->clients as $client) {
            $client->send(json_encode($outgoingMessageArray));
        }
    }

    public function removeMediaItem($from, $incomingMessageArray) {
        echo "Removing YouTube MediaItem (id: " . $incomingMessageArray['mediaItemId'] . ")\n";
        $mediaItem = $this->youtubeFinder->getMediaItemById($incomingMessageArray['mediaItemId']);
        if($mediaItem == $this->currentMediaItem) {
            $this->currentMediaItem = null;
        }
        $this->removeMediaItemFromQueue($mediaItem);
        $this->requestNextMediaItem();
        return array(
            "method" => "userRemovedMediaItem",
            "user" => $from->user->getDisplayName(),

            "mediaItem" => $this->convertMediaItemToArray($mediaItem)
        );
    }

    public function startMediaItem(MediaItem $mediaItem) {
        $this->currentMediaItem = $mediaItem;
        $this->resetMediaItemStartingTimestamp();
        $outgoingMessageArray = array(
            "method" => "startMediaItem",
            "mediaItem" => $this->convertMediaItemToArray($mediaItem)
        );
        foreach ($this->clients as $client) {
            $client->send(json_encode($outgoingMessageArray));
        }
    }

    public function queueIsNotEmpty() {
        return count($this->queue);
    }

    public function resetMediaItemStartingTimestamp() {
        $this->mediaItemStartingTimestamp = microtime(true);
    }

    public function howManySecondsAgoTheMediaItemStarted() {
        return $this->secondsAfterTimestamp($this->mediaItemStartingTimestamp);
    }

    public function userEnergy($conn) {
        if(!isset($conn->energyTimestamp)) {
            return 0;
        }
        $energy = $this->secondsAfterTimestamp($conn->energyTimestamp);
        if($energy > 100) {
            return 100;
        } else {
            return floor($energy);
        }
    }

    public function updateEnergy($conn, $newPoints) {
        $damage = $this->userEnergy($conn) - $newPoints;
        echo $conn->resourceId .": energy is " . $newPoints . "\n";
        $conn->energyTimestamp = microtime(true) - $newPoints;
        $this->sendToEveryone(array( 
            'method' => 'updateEnergyBar',
            'energy' => $this->userEnergy($conn),
            'damage' => $damage,
            'sessionId' => $conn->resourceId
        ));
    }

    public function secondsAfterTimestamp($timestamp) {
        return microtime(true) - $timestamp;
    }

    public function addMediaItem($from, $incomingMessageArray) {
        echo $from->resourceId . " wants to add mediaItem (mediaItemId:" . $incomingMessageArray["id"] . ").\n";
        try { 
            $mediaItem = $this->youtubeFinder->getMediaItemById($incomingMessageArray["id"]);
            $this->addMediaItemToQueue($mediaItem);
            if(count($this->queue) == 1) {
                $this->startQueue();
            }
            return array(
                "method" => "addMediaItem",
                "user" => $from->user->getDisplayName(),
                "mediaItem" => $this->convertMediaItemToArray($mediaItem),

            );
        } catch (Exception $e) {
            return array(
                "method" => "displayError",
                "message" => $e->getMessage()
            ); 
        }
    }    
    
    public function convertMediaItemsToArray($mediaItems) {
        $mediaItemsArray = Array();
        foreach($mediaItems as $mediaItem) {
            $mediaItemsArray[] = $this->convertMediaItemToArray($mediaItem);
        }
        return $mediaItemsArray;
    }

    public function convertMediaItemToArray(MediaItem $mediaItem) {
        return array(
            "id" => $mediaItem->getLink(),
            "title" => $mediaItem->getTitle(),
            "description" => $mediaItem->getDescription(),
            "channelId" => $mediaItem->getChannelId(),
            "channelTitle" => $mediaItem->getChannelTitle(),
            "thumbnail" => $mediaItem->getThumbnail(),
            "duration" => $this->convertSecondsToTime($mediaItem->getDuration())
        );
    }

    public function convertSecondsToTime($seconds) {
        if($seconds > 3600) { 
            return gmdate("H:i:s", $seconds);
        } else {
            return gmdate("i:s", $seconds);
        }
    }

    public function addMediaItemToQueue(MediaItem $mediaItem) {
        echo "Adding mediaItem (mediaItemId:" . $mediaItem->getLink() . ") to queue.\n";
        $this->queue[$mediaItem->getLink()] = $mediaItem;
    }

    public function searchForMediaItem($from, $incomingMessageArray) {
        echo $from->resourceId . " searches youtube for mediaItems: \"" . $incomingMessageArray["searchString"] . "\".\n";
        try { 
            $mediaItems = $this->youtubeFinder->searchForMediaItems($incomingMessageArray["searchString"], 5);
             return array(
                "method" => "showYoutubeResults",
                "mediaItems" => $this->convertMediaItemsToArray($mediaItems)
            );
        } catch (Exception $e) {
            return array(
                "method" => "displayError",
                "message" => $e->getMessage()
            );
        }
    }

    public function searchForPlaylist($from, $incomingMessageArray) {
        echo $from->resourceId . " searches youtube for playlists: \"" . $incomingMessageArray["searchString"] . "\".\n";
        try { 
            $resultArray = $this->youtubeFinder->searchForPlaylists($incomingMessageArray["searchString"], 5);
             return array(
                "method" => "showPlaylistResults",
                "playlists" => $resultArray
            );
        } catch (Exception $e) {
            return array(
                "method" => "displayError",
                "message" => $e->getMessage()
            );
        }
    }    

    public function addPlaylist($from, $incomingMessageArray) {
        echo "Adding playlist (playlistId:" . $incomingMessageArray["id"] . ").\n";
        $mediaItems = $this->youtubeFinder->getMediaItemsByPlaylistId($incomingMessageArray["id"]);
        $returnArray = array();
        $start = false;
        if(count($this->queue) == 0) {
            $start = true;
        } 
        foreach($mediaItems as $mediaItem) {
            $this->addMediaItemToQueue($mediaItem);
            $returnArray[] = $this->convertMediaItemToArray($mediaItem);
        }
        if($start) {
            $this->startQueue();
        }
        return array(
            "method" => "addMediaItems",
            "user" => $from->user->getDisplayName(),
            "mediaItems" => $returnArray,
        );
    }

    public function onError(ConnectionInterface $conn, Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n\n";
        $conn->close();
    }
    

}

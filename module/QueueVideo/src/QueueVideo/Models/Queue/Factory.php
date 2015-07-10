<?php

namespace QueueVideo\Models\Queue;

use QueueVideo\Models\Queue\IdentityMap;
use QueueVideo\Models\Room\Room;

class Factory {
	private $_map;

	public function __construct($identityMap) {
		$this->_map = $identityMap;
	}

	public function getQueueById($id) {
		return $this->_map->fetchQueueById($id);
	}

	public function getQueueByRoom(Room $room) {
		return $this->_map->fetchQueueByRoom($room);
	}

	public function getNewQueue() {
		return new Queue();
	}

}
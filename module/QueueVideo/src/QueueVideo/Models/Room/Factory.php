<?php

namespace QueueVideo\Models\Room;

use QueueVideo\Models\Room\IdentityMap;

class Factory {
	private $_map;

	public function __construct($identityMap) {
		$this->_map = $identityMap;
	}

	public function getRoomById($id) {
		return $this->_map->fetchRoomById($id);
	}

	public function getNewRoom() {
		return new Room();
	}

}
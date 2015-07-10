<?php

namespace QueueVideo\Models\MediaItem;

use QueueVideo\Models\Queue\Queue;

class Factory {
	private $_map;

	public function __construct($identityMap) {
		$this->_map = $identityMap;
	}

	public function getMediaItemById($id) {
		return $this->_map->fetchMediaItemById($id);
	}

	public function getMediaItemsByQueue(Queue $queue) {
		return $this->_map->fetchMediaItemsByQueue($queue);
	}

	public function getNewMediaItem() {
		return new MediaItem();
	}

}
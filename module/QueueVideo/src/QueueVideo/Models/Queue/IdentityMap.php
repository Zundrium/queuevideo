<?php

namespace QueueVideo\Models\Queue;

use QueueVideo\Models\IdentityMapAbstract;
use QueueVideo\Models\Room\Room;


class IdentityMap extends IdentityMapAbstract {
	private $queues;

	public function __construct(DataMapperInterface $dm) {
		$this->queues = array();
		$this->_dataMapper = $dm;
		return $this;
	}

	/**
	* @param $id
	* @return bool
	*/
	private function isQueueCached($id){
		return isset($this->queues[$id]);
	}

	/**
	* @param $id
	* @return Queue
	*/
	public function fetchQueueById($id) {
		if(!$this->isQueueCached($id)) {
			$this->queues[$id] = $this->_dataMapper->fetchQueueById($id);
		}
		return $this->queues[$id];
	}

	/**
	* @param $id
	* @return Queue
	*/
	public function fetchQueueByRoom(Room $room) {
		if(!$this->isQueueCached($room->getQueueId())) {
			$this->queues[$room->getQueueId()] = $this->_dataMapper->fetchQueueByRoom($room);
		}
		return $this->queues[$room->getQueueId()];
	}
}
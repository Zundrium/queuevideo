<?php

namespace QueueVideo\Models\Room;

use QueueVideo\Models\IdentityMapAbstract;

class IdentityMap extends IdentityMapAbstract {
	private $videos;

	public function __construct(DataMapperInterface $dm) {
		$this->videos = array();
		$this->_dataMapper = $dm;
		return $this;
	}

	/**
	* @param $id
	* @return bool
	*/
	private function isRoomCached($id){
		return isset($this->videos[$id]);
	}

	/**
	* @param $id
	* @return Room
	*/
	public function fetchRoomById($id) {
		if(!$this->isRoomCached($id)) {
			$this->videos[$id] = $this->_dataMapper->fetchRoomById($id);
		}
		return $this->videos[$id];
	}
}
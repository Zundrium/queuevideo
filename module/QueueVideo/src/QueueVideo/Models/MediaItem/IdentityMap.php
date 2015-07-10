<?php

namespace QueueVideo\Models\MediaItem;

use QueueVideo\Models\IdentityMapAbstract;
use QueueVideo\Models\Queue\Queue;

class IdentityMap extends IdentityMapAbstract {
	private $videos;
	private $queues;

	public function __construct(DataMapperInterface $dm) {
		$this->videos = array();
		$this->queuess = array();
		$this->_dataMapper = $dm;
		return $this;
	}

	/**
	* @param $id
	* @return bool
	*/
	private function isMediaItemCached($id){
		return isset($this->videos[$id]);
	}

	/**
	* @param $id
	* @return bool
	*/
	private function isQueueCached($id){
		return isset($this->queues[$id]);
	}

	/**
	* @param Queue
	* @return array
	*/
	public function fetchMediaItemsByQueue(Queue $queue) {
		if(!$this->isQueueCached($queue->getId())) {
			$this->queues[$queue->getId()] = $this->_dataMapper->fetchMediaItemsByQueue($queue);
			foreach ($this->queues[$queue->getId()] as $video) {
				if($this->isMediaItemCached($video->getId())) {
					$video = $this->videos[$video->getId()];
				} else {
					$this->videos[$video->getId()] = $video;
				}
			}
		}
		return $this->queues[$queue->getId()];
	}

	/**
	* @param $id
	* @return MediaItem
	*/
	public function fetchMediaItemById($id) {
		if(!$this->isMediaItemCached($id)) {
			$this->videos[$id] = $this->_dataMapper->fetchMediaItemById($id);
		}
		return $this->videos[$id];
	}
}
<?php

namespace QueueVideo\Models\Queue;

use QueueVideo\Models\DataMapperAbstract;
use QueueVideo\Models\Room\Room;


class MySQLDataMapper extends DataMapperAbstract implements DataMapperInterface {

	/**
	* @param $id
	* @return Queue
	*/
	public function fetchQueueById($id) {
		$results = $this->_dbCon->query(
			'SELECT * 
			FROM `queue` 
			WHERE `id` = ?',
			array(
				$id
			)
		);
		$row = $results->current();
		$queue = new Queue();
		$queue->setId($row['id']);
		return $queue;
	}

	/**
	* @param Room
	* @return Queue
	*/
	public function fetchQueueByRoom(Room $room) {
		$results = $this->_dbCon->query(
			'SELECT * 
			FROM `queue` 
			WHERE `id` = ?',
			array(
				$room->getId()
			)
		);
		$row = $results->current();
		$queue = new Queue();
		$queue->setId($row['id']);
		return $queue;
	}

	/**
	* @param Queue
	* @return Queue
	*/
	public function createQueue(Queue $queue) {
		$statement = $this->_dbCon->query(
			'INSERT INTO `queue` (?)',
			array(
				null
			)
		);
		$driver = $this->_dbCon->getDriver();
		$queue->setId($driver->getLastGeneratedValue());
		return $queue;
	}

	/**
	* @param Queue
	* @return void
	*/
	public function deleteQueue(Queue $queue) {
		$statement = $this->_dbCon->query(
			'DELETE FROM `queue` WHERE `id` = ?', 
			array(
				$queue->getId()
			)
		);
	}
}
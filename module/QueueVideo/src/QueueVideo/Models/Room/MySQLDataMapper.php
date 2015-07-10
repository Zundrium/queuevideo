<?php

namespace QueueVideo\Models\Room;

use QueueVideo\Models\DatamapperAbstract;
use Exception;

class MySQLDataMapper extends DataMapperAbstract implements DataMapperInterface {

	/**
	* @param $id
	* @return Room
	*/
	public function fetchRoomById($id) {
		$results = $this->_dbCon->query(
			'SELECT * 
			FROM `room` 
			WHERE `id` = ?',
			array(
				$id
			)
		);
		if($results->count()) { 
			$row = $results->current();
			$room = new Room();
			$room->setId($row['id'])
					->setTitle($row['title'])
					->setPassword($row['password'])
					->setUserLimit($row['user_limit'])
					->setQueueId($row['queue_id']);
			return $room;
		} else {
			throw new Exception("Room not found", 404);
		}
	}

	/**
	* @param Room
	* @return Room
	*/
	public function updateRoom(Room $room) {
		$statement = $this->_dbCon->query(
			'UPDATE `room` SET `queue_id` = ?, `title` = ?, `password` = ?, `user_limit` = ? WHERE `id` = ?',
			array(
				$room->getQueueId(),
				$room->getTitle(),
				$room->getPassword(),
				$room->getUserLimit(),
				$room->getId()
			)
		);
		return $room;
	}

	/**
	* @param Room
	* @return Room
	*/
	public function createRoom(Room $room) {
		$statement = $this->_dbCon->query(
			'INSERT INTO `room` (?,?,?,?,?)',
			array(
				null,
				$room->getQueueId(),
				$room->getTitle(),
				$room->getPassword(),
				$room->getUserLimit()
			)
		);
		$driver = $this->_dbCon->getDriver();
		$room->setId($driver->getLastGeneratedValue());
		return $room;
	}

	/**
	* @param Room
	* @return void
	*/
	public function deleteRoom(Room $room) {
		$statement = $this->_dbCon->query(
			'DELETE FROM `room` WHERE `id` = ?', 
			array(
				$room->getId()
			)
		);
	}
}
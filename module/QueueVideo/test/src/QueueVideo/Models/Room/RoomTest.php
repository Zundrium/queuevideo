<?php

namespace QueueVideoTest\Models\Room;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\Room\Room;

class RoomTest extends PHPUnit_Framework_TestCase { 
	
	public function testObjectPropertiesSet() {
		$id = 1;
		$title = "Title";
		$userlimit = 40;
		$password = "test";
		$queueid = 1;

		$room = new Room();
		$room->setId($id)
				->setPassword($password)
				->setTitle($title)
				->setUserLimit($userlimit)
				->setQueueId($queueid);

		$refRoom = new \ReflectionObject($room);
		$idProp = $refRoom->getProperty('_id');
		$idProp->setAccessible(true);
		$titleProp = $refRoom->getProperty('_title');
		$titleProp->setAccessible(true);
		$passwordProp = $refRoom->getProperty('_password');
		$passwordProp->setAccessible(true);
		$userlimitProp = $refRoom->getProperty('_userlimit');
		$userlimitProp->setAccessible(true);
		$queueIdProp = $refRoom->getProperty('_queueid');
		$queueIdProp->setAccessible(true);

		$this->assertEquals($id, $idProp->getValue($room), "ID not properly set by Room setter method.");
		$this->assertEquals($title, $titleProp->getValue($room), "Title not properly set by Room setter method.");
		$this->assertEquals($password, $passwordProp->getValue($room), "Password not properly set by Room setter method.");
		$this->assertEquals($userlimit, $userlimitProp->getValue($room), "Thumbsup not properly set by Room setter method.");
		$this->assertEquals($queueid, $queueIdProp->getValue($room), "Queue ID not properly set by Room setter method.");
	}

	public function testObjectPropertiesGet(){
		$id = 1;
		$title = "title";
		$password = "test";
		$userlimit = 12;
		$queueid = 1;

		$room = new Room();
		$refRoom = new \ReflectionObject($room);
		$idProp = $refRoom->getProperty('_id');
		$idProp->setAccessible(true);
		$idProp->setValue($room, $id);
		$titleProp = $refRoom->getProperty('_title');
		$titleProp->setAccessible(true);
		$titleProp->setValue($room, $title);
		$passwordProp = $refRoom->getProperty('_password');
		$passwordProp->setAccessible(true);
		$passwordProp->setValue($room, $password);
		$userlimitProp = $refRoom->getProperty('_userlimit');
		$userlimitProp->setAccessible(true);
		$userlimitProp->setValue($room, $userlimit);
		$queueIdProp = $refRoom->getProperty('_queueid');
		$queueIdProp->setAccessible(true);
		$queueIdProp->setValue($room, $queueid);

		$this->assertEquals($id, $room->getId(), "ID not properly returned by Room getter method.");
		$this->assertEquals($title, $room->getTitle(), "Title not properly returned by Room getter method.");
		$this->assertEquals($password, $room->getPassword(), "Password not properly returned by Room getter method.");
		$this->assertEquals($userlimit, $room->getUserLimit(), "Thumbsup not properly returned by Room getter method.");
		$this->assertEquals($queueid, $room->getQueueId(), "Queue ID not properly returned by Room getter method.");
	}
}
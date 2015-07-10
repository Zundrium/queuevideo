<?php

namespace QueueVideoTest\Models\Room;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\Room\Room;
use QueueVideo\Models\Room\IdentityMap;
use QueueVideo\Models\Room\MySQLDataMapper;

class IdentityMapTest extends PHPUnit_Framework_TestCase { 

	public function testIfRoomCanBeFetchedAndCached() {
		$staticProperties = $this->getStaticProperties();
		$room = $this->makeDummyRoom($staticProperties);
		$mockDataMapper = $this->getMockDataMapper();
		$mockDataMapper->expects($this->once())
					->method('fetchRoomById')
					->with($staticProperties['id'])
					->will($this->returnValue($room));
		$this->identityMap = new IdentityMap($mockDataMapper);
		$room = $this->identityMap->fetchRoomById($staticProperties['id']);
		$this->assertInstanceOf('QueueVideo\Models\Room\Room', $room);
		$room2 = $this->identityMap->fetchRoomById($staticProperties['id']);
		$this->assertInstanceOf('QueueVideo\Models\Room\Room', $room2);
	}

	public function getMockDataMapper() {
		$mockDataMapper = $this->getMockBuilder('QueueVideo\Models\Room\MySQLDataMapper')
						->disableOriginalConstructor()
						->getMock();
		return $mockDataMapper;
	}

	public function makeDummyRoom($properties) {
		$room = new Room();
		$room->setId($properties['id'])
				->setPassword($properties['password'])
				->setTitle($properties['title'])
				->setUserLimit($properties['userlimit']);
		return $room;
	}

	public function getStaticProperties() {
		return array (
			'id' => 1,
			'queueid' => 1,
			'title' => 'This is a Title',
			'password' => 'This is a Password',
			'userlimit' => 12
		);
	}

}
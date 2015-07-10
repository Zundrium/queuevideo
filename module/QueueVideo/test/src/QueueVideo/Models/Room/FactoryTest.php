<?php

namespace QueueVideoTest\Models\Room;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\Room\Room;
use QueueVideo\Models\Room\Factory;
use QueueVideo\Models\Room\IdentityMap;

class FactoryTest extends PHPUnit_Framework_TestCase { 
	public function testloadRoom() {
		$id = 1;
		$identityMap = $this->getMockIdentityMap();
		$identityMap->expects($this->once())
						->method('fetchRoomById')
						->with($id)
						->will($this->returnValue(new Room()));
		$factory = new Factory($identityMap);
		$video = $factory->getRoomById($id);
		$this->assertInstanceOf('QueueVideo\Models\Room\Room', $video);
	}

	public function testCreateNewRoom() {
		$identityMap = $this->getMockIdentityMap();
		$factory = new Factory($identityMap);
		$video = $factory->getNewRoom();
		$this->assertInstanceOf('QueueVideo\Models\Room\Room', $video);
	}

	public function getMockIdentityMap() {
		$mockDataMapper = $this->getMockBuilder('QueueVideo\Models\Room\IdentityMap')
						->disableOriginalConstructor()
						->getMock();
		return $mockDataMapper;
	}
}
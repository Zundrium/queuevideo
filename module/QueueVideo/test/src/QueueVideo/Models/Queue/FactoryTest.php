<?php

namespace QueueVideoTest\Models\Queue;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\Queue\Queue;
use QueueVideo\Models\Queue\Factory;
use QueueVideo\Models\Queue\IdentityMap;
use QueueVideo\Models\Room\Room;

class FactoryTest extends PHPUnit_Framework_TestCase { 
	public function testloadQueue() {
		$id = 1;
		$identityMap = $this->getMockIdentityMap();
		$identityMap->expects($this->once())
						->method('fetchQueueById')
						->with($id)
						->will($this->returnValue(new Queue()));
		$factory = new Factory($identityMap);
		$queue = $factory->getQueueById($id);
		$this->assertInstanceOf('QueueVideo\Models\Queue\Queue', $queue);
	}

	public function testloadQueueByRoom() {
		$id = 1;
		$identityMap = $this->getMockIdentityMap();
		$mockRoom = $this->getMockBuilder('QueueVideo\Models\Room\Room')
						->disableOriginalConstructor()
						->getMock();
		$mockRoom->expects($this->any())
					->method('getId')
					->will($this->returnValue($id));
		$identityMap->expects($this->once())
						->method('fetchQueueByRoom')
						->with($mockRoom)
						->will($this->returnValue(new Queue()));
		$factory = new Factory($identityMap);
		$queue = $factory->getQueueByRoom($mockRoom);
		$this->assertInstanceOf('QueueVideo\Models\Queue\Queue', $queue);
	}

	public function testCreateNewQueue() {
		$identityMap = $this->getMockIdentityMap();
		$factory = new Factory($identityMap);
		$queue = $factory->getNewQueue();
		$this->assertInstanceOf('QueueVideo\Models\Queue\Queue', $queue);
	}

	public function getMockIdentityMap() {
		$mockDataMapper = $this->getMockBuilder('QueueVideo\Models\Queue\IdentityMap')
						->disableOriginalConstructor()
						->getMock();
		return $mockDataMapper;
	}
}
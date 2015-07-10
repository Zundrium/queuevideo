<?php

namespace QueueVideoTest\Models\Queue;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\Queue\Queue;
use QueueVideo\Models\Queue\IdentityMap;
use QueueVideo\Models\Queue\MySQLDataMapper;
use QueueVideo\Models\Room\Room;

class IdentityMapTest extends PHPUnit_Framework_TestCase { 

	public function testIfQueueCanBeFetchedAndCached() {
		$staticProperties = $this->getStaticProperties();
		$queue = $this->makeDummyQueue($staticProperties);
		$mockDataMapper = $this->getMockDataMapper();
		$mockDataMapper->expects($this->once())
					->method('fetchQueueById')
					->with($staticProperties['id'])
					->will($this->returnValue($queue));
		$this->identityMap = new IdentityMap($mockDataMapper);
		$queue = $this->identityMap->fetchQueueById($staticProperties['id']);
		$this->assertInstanceOf('QueueVideo\Models\Queue\Queue', $queue);
		$queue2 = $this->identityMap->fetchQueueById($staticProperties['id']);
		$this->assertInstanceOf('QueueVideo\Models\Queue\Queue', $queue2);
	}

	public function testIfQueueCanBeFetchedByRoomAndCached() {
		$staticProperties = $this->getStaticProperties();
		$queue = $this->makeDummyQueue($staticProperties);
		$mockDataMapper = $this->getMockDataMapper();
		$mockRoom = $this->getMockBuilder('QueueVideo\Models\Room\Room')
						->disableOriginalConstructor()
						->getMock();
		$mockRoom->expects($this->any())
					->method('getId')
					->will($this->returnValue($staticProperties['id']));
		$mockDataMapper->expects($this->once())
					->method('fetchQueueByRoom')
					->with($mockRoom)
					->will($this->returnValue($queue));
		$this->identityMap = new IdentityMap($mockDataMapper);
		$queue = $this->identityMap->fetchQueueByRoom($mockRoom);
		$this->assertInstanceOf('QueueVideo\Models\Queue\Queue', $queue);
		$queue2 = $this->identityMap->fetchQueueByRoom($mockRoom);
		$this->assertInstanceOf('QueueVideo\Models\Queue\Queue', $queue2);
	}

	public function getMockDataMapper() {
		$mockDataMapper = $this->getMockBuilder('QueueVideo\Models\Queue\MySQLDataMapper')
						->disableOriginalConstructor()
						->getMock();
		return $mockDataMapper;
	}

	public function makeDummyQueue($properties) {
		$queue = new Queue();
		$queue->setId($properties['id']);
		return $queue;
	}

	public function getStaticProperties() {
		return array (
			'id' => 1
		);
	}

}
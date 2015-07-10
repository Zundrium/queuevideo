<?php

namespace QueueVideoTest\Models\MediaItem;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\MediaItem\Factory;
use QueueVideo\Models\MediaItem\MediaItem;

class FactoryTest extends PHPUnit_Framework_TestCase { 
	public function testloadMediaItem() {
		$id = 1;
		$identityMap = $this->getMockIdentityMap();
		$identityMap->expects($this->once())
						->method('fetchMediaItemById')
						->with($id)
						->will($this->returnValue(new MediaItem()));
		$factory = new Factory($identityMap);
		$video = $factory->getMediaItemById($id);
		$this->assertInstanceOf('QueueVideo\Models\MediaItem\MediaItem', $video);
	}

	public function testloadMediaItemByQueue() {
		$id = 1;
		$identityMap = $this->getMockIdentityMap();
		$mockQueue = $this->getMockBuilder('QueueVideo\Models\Queue\Queue')
						->disableOriginalConstructor()
						->getMock();
		$mockQueue->expects($this->any())
						->method('getId')
						->will($this->returnValue(1));
		$identityMap->expects($this->once())
						->method('fetchMediaItemsByQueue')
						->with($mockQueue)
						->will($this->returnValue(new MediaItem()));
		$factory = new Factory($identityMap);
		$video = $factory->getMediaItemsByQueue($mockQueue);
		$this->assertInstanceOf('QueueVideo\Models\MediaItem\MediaItem', $video);
	}

	public function testCreateNewMediaItem() {
		$identityMap = $this->getMockIdentityMap();
		$factory = new Factory($identityMap);
		$video = $factory->getNewMediaItem();
		$this->assertInstanceOf('QueueVideo\Models\MediaItem\MediaItem', $video);
	}

	public function getMockIdentityMap() {
		$mockDataMapper = $this->getMockBuilder('QueueVideo\Models\MediaItem\IdentityMap')
						->disableOriginalConstructor()
						->getMock();
		return $mockDataMapper;
	}
}
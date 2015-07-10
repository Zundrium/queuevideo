<?php

namespace QueueVideoTest\Models\MediaItem;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\MediaItem\IdentityMap;
use QueueVideo\Models\MediaItem\MediaItem;

class IdentityMapTest extends PHPUnit_Framework_TestCase { 

	public function testIfMediaItemCanBeFetchedAndCached() {
		$staticProperties = $this->getStaticProperties();
		$video = $this->makeDummyMediaItem($staticProperties);
		$mockDataMapper = $this->getMockDataMapper();
		$mockDataMapper->expects($this->once())
					->method('fetchMediaItemById')
					->with($staticProperties['id'])
					->will($this->returnValue($video));
		$this->identityMap = new IdentityMap($mockDataMapper);
		$video = $this->identityMap->fetchMediaItemById($staticProperties['id']);
		$this->assertInstanceOf('QueueVideo\Models\MediaItem\MediaItem', $video);
		$video2 = $this->identityMap->fetchMediaItemById($staticProperties['id']);
		$this->assertInstanceOf('QueueVideo\Models\MediaItem\MediaItem', $video2);
		$this->checkIfMediaItemsAreReferenced($video,$video2);
	}

	public function testMediaItemsCanBeFetchedAndCached() {
		$staticProperties = $this->getStaticProperties();
		$videos = array(
			$this->makeDummyMediaItem($staticProperties),
			$this->makeDummyMediaItem($staticProperties, 2),
			$this->makeDummyMediaItem($staticProperties, 3)
		);
		$mockQueue = $this->getMockBuilder('QueueVideo\Models\Queue\Queue')
						->disableOriginalConstructor()
						->getMock();
		$mockQueue->expects($this->any())
						->method('getId')
						->will($this->returnValue(1));
		$mockDataMapper = $this->getMockDataMapper();
		$mockDataMapper->expects($this->once())
						->method('fetchMediaItemsByQueue')
						->with($mockQueue)
						->will($this->returnValue($videos));
		$this->identityMap = new IdentityMap($mockDataMapper);
		$videos = $this->identityMap->fetchMediaItemsByQueue($mockQueue);
		$videos2 = $this->identityMap->fetchMediaItemsByQueue($mockQueue);
		for ($videoCounter = 0; $videoCounter < count($videos); $videoCounter++) { 
			$this->checkIfMediaItemsAreReferenced($videos[$videoCounter] ,$videos2[$videoCounter]);
		}
	}

	public function checkIfMediaItemsAreReferenced($video, $video2) {
		$refMediaItem = new \ReflectionObject($video);
		$newTitleProp = $refMediaItem->getProperty('_title');
		$newTitleProp->setAccessible(true);
		$newTitleProp->setValue($video, 'test');

		$titleProp = $refMediaItem->getProperty('_title');
		$titleProp->setAccessible(true);

		$refMediaItem2 = new \ReflectionObject($video2);
		$titleProp2 = $refMediaItem2->getProperty('_title');
		$titleProp2->setAccessible(true);

		$this->assertSame($titleProp2->getValue($video2), $titleProp->getValue($video), "MediaItem is not referenced by Identity map.");
	}

	public function getMockDataMapper() {
		$mockDataMapper = $this->getMockBuilder('QueueVideo\Models\MediaItem\MySQLDataMapper')
						->disableOriginalConstructor()
						->getMock();
		return $mockDataMapper;
	}

	public function makeDummyMediaItem($properties, $customId = 1) {
		$video = new MediaItem();
		$video->setId($customId)
				->setLink($properties['link'])
				->setTitle($properties['title'])
				->setThumbsUp($properties['thumbsup'])
				->setThumbsDown($properties['thumbsdown']);
		return $video;
	}

	public function getStaticProperties() {
		return array (
			'id' => 1,
			'title' => 'This is a Title',
			'link' => 'This is a Link',
			'thumbsup' => 12,
			'thumbsdown' => 4
		);
	}

}
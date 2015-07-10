<?php 

namespace QueueVideoTest\Models\MediaItem;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\MediaItem\MySQLDataMapper;
use Zend\Db\ResultSet\ResultSet;
use QueueVideo\Models\MediaItem\MediaItem;

class MySQLDataMapperTest extends PHPUnit_Framework_TestCase {

	public function testFetchMediaItemById() {
		$staticProperties = $this->getStaticProperties();
		$mockAdapter = $this->getMockAdapterWithResult(
			array(
				array(
					'id'			=> $staticProperties['id'],
					'title'			=> $staticProperties['title'],
					'link'			=> $staticProperties['link'],
					'thumbs_up'		=> $staticProperties['thumbsup'],
					'thumbs_down'	=> $staticProperties['thumbsdown'],
					'channel_id' 	=> $staticProperties['channelid'],
					'channel_title' => $staticProperties['channeltitle'],
					'thumbnail' 	=> $staticProperties['thumbnail'],
					'description' 	=> $staticProperties['description'],
					'duration' 		=> $staticProperties['duration']
				)
			)	
		);
		$MySQLDataMapper 	= new MySQLDataMapper($mockAdapter);
		$video 				= $MySQLDataMapper->fetchMediaItemById($staticProperties['id']);
		$properties 		= $this->getReflectedPropertiesOfMediaItem($video);
		$this->checkIfPropertiesAreSetCorrectly($staticProperties, $properties, $video);
	}

	public function testFetchMediaItemByQueue() {
		$staticProperties 	= $this->getStaticProperties();
		$mockAdapter 		= $this->getMockAdapterWithResult(
			array(
				array(
					'id'			=> $staticProperties['id'],
					'title'			=> $staticProperties['title'],
					'link'			=> $staticProperties['link'],
					'thumbs_up'		=> $staticProperties['thumbsup'],
					'thumbs_down'	=> $staticProperties['thumbsdown'],
					'channel_id' 	=> $staticProperties['channelid'],
					'channel_title' => $staticProperties['channeltitle'],
					'thumbnail' 	=> $staticProperties['thumbnail'],
					'description' 	=> $staticProperties['description'],
					'duration' 		=> $staticProperties['duration']
				),
				array(
					'id'			=> $staticProperties['id']+1,
					'title'			=> $staticProperties['title'],
					'link'			=> $staticProperties['link'],
					'thumbs_up'		=> $staticProperties['thumbsup'],
					'thumbs_down'	=> $staticProperties['thumbsdown'],
					'channel_id' 	=> $staticProperties['channelid'],
					'channel_title' => $staticProperties['channeltitle'],
					'thumbnail' 	=> $staticProperties['thumbnail'],
					'description' 	=> $staticProperties['description'],
					'duration' 		=> $staticProperties['duration']
				),
				array(
					'id'			=> $staticProperties['id']+2,
					'title'			=> $staticProperties['title'],
					'link'			=> $staticProperties['link'],
					'thumbs_up'		=> $staticProperties['thumbsup'],
					'thumbs_down'	=> $staticProperties['thumbsdown'],
					'channel_id' 	=> $staticProperties['channelid'],
					'channel_title' => $staticProperties['channeltitle'],
					'thumbnail' 	=> $staticProperties['thumbnail'],
					'description' 	=> $staticProperties['description'],
					'duration' 		=> $staticProperties['duration']
				)
			)	
		);

		$mockQueue = $this->getMockBuilder('QueueVideo\Models\Queue\Queue')
						->disableOriginalConstructor()
						->getMock();
		$mockQueue->expects($this->any())
						->method('getId')
						->will($this->returnValue(1));
		$MySQLDataMapper = new MySQLDataMapper($mockAdapter);
		$videos = $MySQLDataMapper->fetchMediaItemsByQueue($mockQueue);
		$counter = 1;
		foreach ($videos as $video) {
			$properties = $this->getReflectedPropertiesOfMediaItem($video);
			$staticProperties['id'] = $counter;
			$this->checkIfPropertiesAreSetCorrectly($staticProperties, $properties, $video);	
			$counter++;
		}
	}

	public function testUpdateMediaItem() {
		$staticProperties 	= $this->getStaticProperties();
		$mockAdapter 		= $this->getMockAdapter();
		$mockAdapter->expects($this->once())
					->method('query')
					->with(
						'UPDATE `video` SET `title` = ?, `link` = ?, `thumbs_up` = ?, `thumbs_down` = ?, `channel_id` = ?, `channel_title` = ?, `thumbnail` = ?, `description` = ?, `duration` = ?  WHERE `id` = ?',
						array(
							$staticProperties['title'],
							$staticProperties['link'],
							$staticProperties['thumbsup'],
							$staticProperties['thumbsdown'],
							$staticProperties['channelid'],
							$staticProperties['channeltitle'],
							$staticProperties['thumbnail'],
							$staticProperties['description'],
							$staticProperties['duration'],
							$staticProperties['id']
						)
					);
		
		$MySQLDataMapper 	= new MySQLDataMapper($mockAdapter);
		$video 				= $this->makeDummyMediaItem($staticProperties);
		$video 				= $MySQLDataMapper->updateMediaItem($video);
		$properties 		= $this->getReflectedPropertiesOfMediaItem($video);
		$this->checkIfPropertiesAreSetCorrectly($staticProperties, $properties, $video);
	}

	public function testDeleteMediaItem() {
		$staticProperties 	= $this->getStaticProperties();
		$mockAdapter 		= $this->getMockAdapter();
		$mockAdapter->expects($this->once())
					->method('query')
					->with(
						'DELETE FROM `video` WHERE `id` = ?',
						array(
							$staticProperties['id']
						)
					);
		$video 				= $this->makeDummyMediaItem($staticProperties);
		$MySQLDataMapper 	= new MySQLDataMapper($mockAdapter);
		$return 			= $MySQLDataMapper->deleteMediaItem($video);
		$this->assertEquals(null, $return, "DeleteMediaItem method returns something else then null in MySQLDataMapper.");
	}

	public function testCreateMediaItem() {
		$staticProperties 	= $this->getStaticProperties();
		$mockAdapter 		= $this->getMockAdapter();
		$mockDriver 		= $this->getMockBuilder('Zend\Db\Adapter\Driver\Pdo\Pdo')
						->disableOriginalConstructor()
						->getMock();
		$mockDriver->expects($this->once())
					->method('getLastGeneratedValue')
					->will($this->returnValue($staticProperties['id']));
		$mockAdapter->expects($this->once())
					->method('query')
					->with(
						'INSERT INTO `video` (?,?,?,?,?,?,?,?,?,?)',
						array(
							null,
							$staticProperties['title'],
							$staticProperties['link'],
							$staticProperties['thumbsup'],
							$staticProperties['thumbsdown'],
							$staticProperties['channelid'],
							$staticProperties['channeltitle'],
							$staticProperties['thumbnail'],
							$staticProperties['description'],
							$staticProperties['duration']
						)
					);
		$mockAdapter->expects($this->once())
					->method('getDriver')
					->will($this->returnValue($mockDriver));
		$video 				= $this->makeDummyMediaItem($staticProperties);
		$MySQLDataMapper 	= new MySQLDataMapper($mockAdapter);
		$video 				= $MySQLDataMapper->createMediaItem($video);
		$properties 		= $this->getReflectedPropertiesOfMediaItem($video);
	}

	public function checkIfPropertiesAreSetCorrectly($staticProperties, $reflectedProperties, $video) {
		$this->assertEquals($staticProperties['id'], $reflectedProperties['idProp']->getValue($video), "ID not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['title'], $reflectedProperties['titleProp']->getValue($video), "Title not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['link'], $reflectedProperties['linkProp']->getValue($video), "Link not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['thumbsup'], $reflectedProperties['thumbsupProp']->getValue($video), "Thumbsup not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['thumbsdown'], $reflectedProperties['thumbsdownProp']->getValue($video), "Thumbsdown not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['channelid'], $reflectedProperties['channelidProp']->getValue($video), "channelid not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['channeltitle'], $reflectedProperties['channeltitleProp']->getValue($video), "channeltitle not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['thumbnail'], $reflectedProperties['thumbnailProp']->getValue($video), "thumbnail not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['description'], $reflectedProperties['descriptionProp']->getValue($video), "description not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['duration'], $reflectedProperties['durationProp']->getValue($video), "duration not properly set in MySQLDataMapper.");
	}

	public function getStaticProperties() {
		return array (
			'id' => 1,
			'title' => 'This is a Title',
			'link' => 'This is a Link',
			'thumbsup' => 12,
			'thumbsdown' => 4,
		'channelid' => "ueoueoueohtn13",
		'channeltitle' => "channes tieont",
		'thumbnail' => "ueaunehtoaunht",
		'description' => "euohtnsuhetaoutnehnoaueohtauhtneoaunhteoanht
		ueouhtenoauhteo	a
		ueaohueoa
		heuaoueoaueohtnauhtneoau
		eoauhteoauhtneonhtaunehto
		auhteaouehtnoauneoahtnueona
		uehtaouhteoahuneohauhneoahtn",
		'duration' => 123213
		);
	}

	public function makeDummyMediaItem($properties) {
		$video = new MediaItem();
		$video->setId($properties['id'])
				->setLink($properties['link'])
				->setTitle($properties['title'])
				->setThumbsUp($properties['thumbsup'])
				->setThumbsDown($properties['thumbsdown'])
				->setChannelId($properties['channelid'])
				->setChannelTitle($properties['channeltitle'])
				->setThumbnail($properties['thumbnail'])
				->setDescription($properties['description'])
				->setDuration($properties['duration']);
		return $video;
	}

	public function getReflectedPropertiesOfMediaItem($video) {
		$refMediaItem = new \ReflectionObject($video);
		$properties = array();
		$properties['idProp'] = $refMediaItem->getProperty('_id');
		$properties['idProp']->setAccessible(true);
		$properties['titleProp'] = $refMediaItem->getProperty('_title');
		$properties['titleProp']->setAccessible(true);
		$properties['linkProp'] = $refMediaItem->getProperty('_link');
		$properties['linkProp']->setAccessible(true);
		$properties['thumbsupProp'] = $refMediaItem->getProperty('_thumbsup');
		$properties['thumbsupProp']->setAccessible(true);
		$properties['thumbsdownProp'] = $refMediaItem->getProperty('_thumbsdown');
		$properties['thumbsdownProp']->setAccessible(true);
		$properties['channelidProp'] = $refMediaItem->getProperty('_channelid');
		$properties['channelidProp']->setAccessible(true);
		$properties['channeltitleProp'] = $refMediaItem->getProperty('_channeltitle');
		$properties['channeltitleProp']->setAccessible(true);
		$properties['thumbnailProp'] = $refMediaItem->getProperty('_thumbnail');
		$properties['thumbnailProp']->setAccessible(true);
		$properties['descriptionProp'] = $refMediaItem->getProperty('_description');
		$properties['descriptionProp']->setAccessible(true);
		$properties['durationProp'] = $refMediaItem->getProperty('_duration');
		$properties['durationProp']->setAccessible(true);

		return $properties;
	}

	public function getMockAdapter() {
		$mockAdapter = $this->getMockBuilder('Zend\Db\Adapter\Adapter')
						->setConstructorArgs(
							array(
								array(
									'driver' => 'Pdo',
									'database' => 'zend_db_example',
									'username' => 'developer',
									'password' => 'developer-password'
								)
							)
						)
						->getMock();
		return $mockAdapter;
	}

	public function getMockAdapterWithResult($returnValue) {
		$resultSet = new ResultSet();
		$resultSet->initialize($returnValue);
		$mockAdapter = $this->getMockAdapter();
		$mockAdapter->expects($this->once())
						->method('query')
						->will($this->returnValue($resultSet));
		return $mockAdapter;
	}
}
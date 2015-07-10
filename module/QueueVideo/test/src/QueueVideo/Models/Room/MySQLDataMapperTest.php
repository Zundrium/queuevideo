<?php 

namespace QueueRoomTest\Models\Room;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\Room\MySQLDataMapper;
use QueueVideo\Models\Room\Room;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class MySQLDataMapperTest extends PHPUnit_Framework_TestCase {

	public function testFetchRoomById() {
		$staticProperties = $this->getStaticProperties();
		$mockAdapter = $this->getMockAdapterWithResult(
			array(
				array(
					'id'			=> $staticProperties['id'],
					'queue_id'		=> $staticProperties['queueid'],
					'password'		=> $staticProperties['password'],
					'title'			=> $staticProperties['title'],
					'user_limit'	=> $staticProperties['userlimit']
				)
			)	
		);
		$MySQLDataMapper = new MySQLDataMapper($mockAdapter);
		$room = $MySQLDataMapper->fetchRoomById($staticProperties['id']);
		$properties = $this->getReflectedPropertiesOfRoom($room);
		$this->checkIfPropertiesAreSetCorrectly($staticProperties, $properties, $room);
	}

	public function testUpdateRoom() {
		$staticProperties = $this->getStaticProperties();
		$mockAdapter = $this->getMockAdapter();
		$mockAdapter->expects($this->once())
					->method('query')
					->with(	
						'UPDATE `room` SET `queue_id` = ?, `title` = ?, `password` = ?, `user_limit` = ? WHERE `id` = ?',
						array(
							$staticProperties['queueid'],
							$staticProperties['title'],
							$staticProperties['password'],
							$staticProperties['userlimit'],
							$staticProperties['id']
						)
					);
		
		$MySQLDataMapper = new MySQLDataMapper($mockAdapter);
		$room = $this->makeDummyRoom($staticProperties);
		$room = $MySQLDataMapper->updateRoom($room);
		$properties = $this->getReflectedPropertiesOfRoom($room);
		$this->checkIfPropertiesAreSetCorrectly($staticProperties, $properties, $room);
	}

	public function testDeleteRoom() {
		$staticProperties = $this->getStaticProperties();
		$mockAdapter = $this->getMockAdapter();
		$mockAdapter->expects($this->once())
					->method('query')
					->with(
						'DELETE FROM `room` WHERE `id` = ?',
						array(
							$staticProperties['id']
						)
					);
		$room = $this->makeDummyRoom($staticProperties);
		$MySQLDataMapper = new MySQLDataMapper($mockAdapter);
		$return = $MySQLDataMapper->deleteRoom($room);
		$this->assertEquals(null, $return, "DeleteRoom method returns something else then null in MySQLDataMapper.");
	}

	public function testCreateRoom() {
		$staticProperties = $this->getStaticProperties();
		$mockAdapter = $this->getMockAdapter();
		$mockDriver = $this->getMockBuilder('Zend\Db\Adapter\Driver\Pdo\Pdo')
						->disableOriginalConstructor()
						->getMock();
		$mockDriver->expects($this->once())
					->method('getLastGeneratedValue')
					->will($this->returnValue($staticProperties['id']));
		$mockAdapter->expects($this->once())
					->method('query')
					->with(
						'INSERT INTO `room` (?,?,?,?,?)',
						array(
							null,
							$staticProperties['queueid'],
							$staticProperties['title'],
							$staticProperties['password'],
							$staticProperties['userlimit']
						)
					);
		$mockAdapter->expects($this->once())
					->method('getDriver')
					->will($this->returnValue($mockDriver));
		$room = $this->makeDummyRoom($staticProperties);
		$MySQLDataMapper = new MySQLDataMapper($mockAdapter);
		$room = $MySQLDataMapper->createRoom($room);
		$properties = $this->getReflectedPropertiesOfRoom($room);
	}

	public function checkIfPropertiesAreSetCorrectly($staticProperties, $reflectedProperties, $room) {
		$this->assertEquals($staticProperties['id'], $reflectedProperties['idProp']->getValue($room), "ID not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['queueid'], $reflectedProperties['queueIdProp']->getValue($room), "Queue ID not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['password'], $reflectedProperties['passwordProp']->getValue($room), "Password not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['title'], $reflectedProperties['titleProp']->getValue($room), "Title not properly set in MySQLDataMapper.");
		$this->assertEquals($staticProperties['userlimit'], $reflectedProperties['userlimitProp']->getValue($room), "Thumbsup not properly set in MySQLDataMapper.");
	}

	public function getStaticProperties() {
		return array (
			'id' => 1,
			'queueid' => 1,
			'password' => 'This is a Password',
			'title' => 'This is a Title',
			'userlimit' => 12
		);
	}

	public function makeDummyRoom($properties) {
		$room = new Room();
		$room->setId($properties['id'])
				->setTitle($properties['title'])
				->setPassword($properties['password'])
				->setUserLimit($properties['userlimit'])
				->setQueueId($properties['queueid']);
		return $room;
	}

	public function getReflectedPropertiesOfRoom($room) {
		$refRoom = new \ReflectionObject($room);
		$properties = array();
		$properties['idProp'] = $refRoom->getProperty('_id');
		$properties['idProp']->setAccessible(true);
		$properties['queueIdProp'] = $refRoom->getProperty('_queueid');
		$properties['queueIdProp']->setAccessible(true);
		$properties['passwordProp'] = $refRoom->getProperty('_password');
		$properties['passwordProp']->setAccessible(true);
		$properties['titleProp'] = $refRoom->getProperty('_title');
		$properties['titleProp']->setAccessible(true);
		$properties['userlimitProp'] = $refRoom->getProperty('_userlimit');
		$properties['userlimitProp']->setAccessible(true);
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
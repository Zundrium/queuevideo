<?php 

namespace QueueVideoTest\Models\Queue;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\Queue\MySQLDataMapper;
use QueueVideo\Models\Queue\Queue;
use QueueVideo\Models\Room\Room;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class MySQLDataMapperTest extends PHPUnit_Framework_TestCase {

	public function testFetchQueueById() {
		$staticProperties = $this->getStaticProperties();
		$mockAdapter = $this->getMockAdapterWithResult(
			array(
				array(
					'id' => $staticProperties['id']
				)
			)	
		);
		$MySQLDataMapper = new MySQLDataMapper($mockAdapter);
		$queue = $MySQLDataMapper->fetchQueueById($staticProperties['id']);
		$properties = $this->getReflectedPropertiesOfQueue($queue);
		$this->checkIfPropertiesAreSetCorrectly($staticProperties, $properties, $queue);
	}

	public function testFetchQueueByRoom() {
		$staticProperties = $this->getStaticProperties();
		$mockRoom = $this->getMockBuilder('QueueVideo\Models\Room\Room')
						->disableOriginalConstructor()
						->getMock();
		$mockRoom->expects($this->once())
					->method('getId')
					->will($this->returnValue($staticProperties['id']));
		$mockAdapter = $this->getMockAdapterWithResult(
			array(
				array(
					'id' => $staticProperties['id']
				)
			)	
		);
		$MySQLDataMapper = new MySQLDataMapper($mockAdapter);
		$queue = $MySQLDataMapper->fetchQueueByRoom($mockRoom);
		$properties = $this->getReflectedPropertiesOfQueue($queue);
		$this->checkIfPropertiesAreSetCorrectly($staticProperties, $properties, $queue);
	}

	public function testCreateQueue() {
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
						'INSERT INTO `queue` (?)',
						array(
							null
						)
					);
		$mockAdapter->expects($this->once())
					->method('getDriver')
					->will($this->returnValue($mockDriver));
		$queue = $this->makeDummyQueue($staticProperties);
		$MySQLDataMapper = new MySQLDataMapper($mockAdapter);
		$queue = $MySQLDataMapper->createQueue($queue);
		$properties = $this->getReflectedPropertiesOfQueue($queue);
	}

	public function checkIfPropertiesAreSetCorrectly($staticProperties, $reflectedProperties, $queue) {
		$this->assertEquals($staticProperties['id'], $reflectedProperties['idProp']->getValue($queue), "ID not properly set in MySQLDataMapper.");
	}

	public function getStaticProperties() {
		return array (
			'id' => 1
		);
	}

	public function makeDummyQueue($properties) {
		$queue = new Queue();
		$queue->setId($properties['id']);
		return $queue;
	}

	public function getReflectedPropertiesOfQueue($queue) {
		$refQueue = new \ReflectionObject($queue);
		$properties = array();
		$properties['idProp'] = $refQueue->getProperty('_id');
		$properties['idProp']->setAccessible(true);
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
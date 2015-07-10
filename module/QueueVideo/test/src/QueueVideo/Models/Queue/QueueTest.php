<?php

namespace QueueVideoTest\Models\Queue;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\Queue\Queue;

class QueueTest extends PHPUnit_Framework_TestCase { 
	
	public function testObjectPropertiesSet() {
		$id = 1;
		$queue = new Queue();
		$queue->setId($id);
		$refQueue = new \ReflectionObject($queue);
		$idProp = $refQueue->getProperty('_id');
		$idProp->setAccessible(true);
		$this->assertEquals($id, $idProp->getValue($queue), "ID not properly set by Queue setter method.");
	}

	public function testObjectPropertiesGet(){
		$id = 1;
		$queue = new Queue();
		$refQueue = new \ReflectionObject($queue);
		$idProp = $refQueue->getProperty('_id');
		$idProp->setAccessible(true);
		$idProp->setValue($queue, $id);
		$this->assertEquals($id, $queue->getId(), "ID not properly returned by Queue getter method.");
	}
}
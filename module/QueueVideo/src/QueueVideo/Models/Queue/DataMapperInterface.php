<?php

namespace QueueVideo\Models\Queue;

interface DataMapperInterface {
	/**
	* @param $id
	* @return Queue
	*/
	public function fetchQueueById($id);
	/**
	* @param Queue
	* @return Queue
	*/
	public function createQueue(Queue $video);
	/**
	* @param Queue
	* @return void
	*/
	public function deleteQueue(Queue $video);
}
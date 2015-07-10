<?php

namespace QueueVideo\Models\MediaItem;

use QueueVideo\Models\DataMapperAbstract;
use QueueVideo\Models\Queue\Queue;
use Exception;

class MySQLDataMapper extends DataMapperAbstract implements DataMapperInterface {

	/**
	* @param $id
	* @return MediaItem
	*/
	public function fetchMediaItemById($id) {
		$results = $this->_dbCon->query(
			'SELECT * 
			FROM `video` 
			WHERE `id` = ?',
			array(
				$id
			)
		);

		if($results->count()) { 
			$row = $results->current();
			$video = new MediaItem();
			$video->setId($row['id'])
					->setTitle($row['title'])
					->setLink($row['link'])
					->setThumbsUp($row['thumbs_up'])
					->setThumbsDown($row['thumbs_down'])
					->setChannelId($row['channel_id'])
					->setChannelTitle($row['channel_title'])
					->setThumbnail($row['thumbnail'])
					->setDescription($row['description'])
					->setDuration($row['duration']);
			return $video;
		} else {
			throw new Exception("MediaItem not found", 404);
		}
	}

	/**
	* @param Queue
	* @return array
	*/
	public function fetchMediaItemsByQueue(Queue $queue) {
		$results = $this->_dbCon->query(
			'SELECT * 
			FROM `video` 
			WHERE `id` = ?',
			array(
				$queue->getId()
			)
		);
		if($results->count()) { 
			$videos = array();
			foreach($results as $row) {
				$video = new MediaItem();
				$video->setId($row['id'])
					->setTitle($row['title'])
					->setLink($row['link'])
					->setThumbsUp($row['thumbs_up'])
					->setThumbsDown($row['thumbs_down'])
					->setChannelId($row['channel_id'])
					->setChannelTitle($row['channel_title'])
					->setThumbnail($row['thumbnail'])
					->setDescription($row['description'])
					->setDuration($row['duration']);
				$videos[] = $video;
			}
			return $videos;
		} else {
			throw new Exception("MediaItems not found", 404);
		}
	}

	/**
	* @param MediaItem
	* @return MediaItem
	*/
	public function updateMediaItem(MediaItem $video) {
		$statement = $this->_dbCon->query(
			'UPDATE `video` SET `title` = ?, `link` = ?, `thumbs_up` = ?, `thumbs_down` = ?, `channel_id` = ?, `channel_title` = ?, `thumbnail` = ?, `description` = ?, `duration` = ?  WHERE `id` = ?',
			array(
				$video->getTitle(),
				$video->getLink(),
				$video->getThumbsUp(),
				$video->getThumbsDown(),
				$video->getChannelId(),
				$video->getChannelTitle(),
				$video->getThumbnail(),
				$video->getDescription(),
				$video->getDuration(),
				$video->getId()
			)
		);
		return $video;
	}

	/**
	* @param MediaItem
	* @return MediaItem
	*/
	public function createMediaItem(MediaItem $video) {
		$statement = $this->_dbCon->query(
			'INSERT INTO `video` (?,?,?,?,?,?,?,?,?,?)',
			array(
				null,
				$video->getTitle(),
				$video->getLink(),
				$video->getThumbsUp(),
				$video->getThumbsDown(),
				$video->getChannelId(),
				$video->getChannelTitle(),
				$video->getThumbnail(),
				$video->getDescription(),
				$video->getDuration()
			)
		);
		$driver = $this->_dbCon->getDriver();
		$video->setId($driver->getLastGeneratedValue());
		return $video;
	}

	/**
	* @param MediaItem
	* @return void
	*/
	public function deleteMediaItem(MediaItem $video) {
		$statement = $this->_dbCon->query(
			'DELETE FROM `video` WHERE `id` = ?', 
			array(
				$video->getId()
			)
		);
	}
}
<?php

namespace QueueVideo\Models\MediaItem;

interface DataMapperInterface {
	/**
	* @param $id
	* @return MediaItem
	*/
	public function fetchMediaItemById($id);
	/**
	* @param MediaItem
	* @return MediaItem
	*/
	public function updateMediaItem(MediaItem $video);
	/**
	* @param MediaItem
	* @return MediaItem
	*/
	public function createMediaItem(MediaItem $video);
	/**
	* @param MediaItem
	* @return void
	*/
	public function deleteMediaItem(MediaItem $video);
}
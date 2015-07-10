<?php

namespace QueueVideo\Models\Room;

interface DataMapperInterface {
	/**
	* @param $id
	* @return Room
	*/
	public function fetchRoomById($id);
	/**
	* @param Room
	* @return Room
	*/
	public function updateRoom(Room $video);
	/**
	* @param Room
	* @return Room
	*/
	public function createRoom(Room $video);
	/**
	* @param Room
	* @return void
	*/
	public function deleteRoom(Room $video);
}
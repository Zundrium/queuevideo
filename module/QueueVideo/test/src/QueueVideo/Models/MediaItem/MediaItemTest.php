<?php

namespace QueueVideoTest\Models\MediaItem;

use PHPUnit_Framework_TestCase;
use QueueVideo\Models\MediaItem\MediaItem;

class MediaItemTest extends PHPUnit_Framework_TestCase { 
	
	public function testObjectPropertiesSet() {
		$id = 1;
		$title = "title";
		$link = "test";
		$thumbsup = 12;
		$thumbsdown = 4;
		$channelid = "ueoueoueohtn13";
		$channeltitle = "channes tieont";
		$thumbnail = "ueaunehtoaunht";
		$description = "euohtnsuhetaoutnehnoaueohtauhtneoaunhteoanht
		ueouhtenoauhteoa
		ueaohueoa
		heuaoueoaueohtnauhtneoau
		eoauhteoauhtneonhtaunehto
		auhteaouehtnoauneoahtnueona
		uehtaouhteoahuneohauhneoahtn";
		$duration = 123213;
 
		$video = new MediaItem();
		$video->setId($id);
		$video->setLink($link);
		$video->setTitle($title);
		$video->setThumbsUp($thumbsup);
		$video->setThumbsDown($thumbsdown);
		$video->setChannelId($channelid);
		$video->setChannelTitle($channeltitle);
		$video->setThumbnail($thumbnail);
		$video->setDescription($description);
		$video->setDuration($duration);

		$refMediaItem = new \ReflectionObject($video);
		$idProp = $refMediaItem->getProperty('_id');
		$idProp->setAccessible(true);
		$titleProp = $refMediaItem->getProperty('_title');
		$titleProp->setAccessible(true);
		$linkProp = $refMediaItem->getProperty('_link');
		$linkProp->setAccessible(true);
		$thumbsupProp = $refMediaItem->getProperty('_thumbsup');
		$thumbsupProp->setAccessible(true);
		$thumbsdownProp = $refMediaItem->getProperty('_thumbsdown');
		$thumbsdownProp->setAccessible(true);
		$channelidProp = $refMediaItem->getProperty('_channelid');
		$channelidProp->setAccessible(true);
		$channeltitleProp = $refMediaItem->getProperty('_channeltitle');
		$channeltitleProp->setAccessible(true);
		$thumbnailProp = $refMediaItem->getProperty('_thumbnail');
		$thumbnailProp->setAccessible(true);
		$descriptionProp = $refMediaItem->getProperty('_description');
		$descriptionProp->setAccessible(true);
		$durationProp = $refMediaItem->getProperty('_duration');
		$durationProp->setAccessible(true);

		$this->assertEquals($id, $idProp->getValue($video), "ID not properly set by MediaItem setter method.");
		$this->assertEquals($title, $titleProp->getValue($video), "Title not properly set by MediaItem setter method.");
		$this->assertEquals($link, $linkProp->getValue($video), "Link not properly set by MediaItem setter method.");
		$this->assertEquals($thumbsup, $thumbsupProp->getValue($video), "Thumbsup not properly set by MediaItem setter method.");
		$this->assertEquals($thumbsdown, $thumbsdownProp->getValue($video), "Thumbsdown not properly set by MediaItem setter method.");
		$this->assertEquals($channelid, $channelidProp->getValue($video), "Channel ID not properly set by MediaItem setter method.");
		$this->assertEquals($channeltitle, $channeltitleProp->getValue($video), "channeltitle not properly set by MediaItem setter method.");
		$this->assertEquals($thumbnail, $thumbnailProp->getValue($video), "thumbnail not properly set by MediaItem setter method.");
		$this->assertEquals($description, $descriptionProp->getValue($video), "description not properly set by MediaItem setter method.");
		$this->assertEquals($duration, $durationProp->getValue($video), "duration not properly set by MediaItem setter method.");
	}

	public function testObjectPropertiesGet(){
		$id = 1;
		$title = "title";
		$link = "test";
		$thumbsup = 12;
		$thumbsdown = 4;
		$channelid = "ueoueoueohtn13";
		$channeltitle = "channes tieont";
		$thumbnail = "ueaunehtoaunht";
		$description = "euohtnsuhetaoutnehnoaueohtauhtneoaunhteoanht
		ueouhtenoauhteoa
		ueaohueoa
		heuaoueoaueohtnauhtneoau
		eoauhteoauhtneonhtaunehto
		auhteaouehtnoauneoahtnueona
		uehtaouhteoahuneohauhneoahtn";
		$duration = 123213;

		$video = new MediaItem();
		$refMediaItem = new \ReflectionObject($video);
		$idProp = $refMediaItem->getProperty('_id');
		$idProp->setAccessible(true);
		$idProp->setValue($video, $id);
		$titleProp = $refMediaItem->getProperty('_title');
		$titleProp->setAccessible(true);
		$titleProp->setValue($video, $title);
		$linkProp = $refMediaItem->getProperty('_link');
		$linkProp->setAccessible(true);
		$linkProp->setValue($video, $link);
		$thumbsupProp = $refMediaItem->getProperty('_thumbsup');
		$thumbsupProp->setAccessible(true);
		$thumbsupProp->setValue($video, $thumbsup);
		$thumbsdownProp = $refMediaItem->getProperty('_thumbsdown');
		$thumbsdownProp->setAccessible(true);
		$thumbsdownProp->setValue($video, $thumbsdown);

		$channelidProp = $refMediaItem->getProperty('_channelid');
		$channelidProp->setAccessible(true);
		$channelidProp->setValue($video, $channelid);

		$channeltitleProp = $refMediaItem->getProperty('_channeltitle');
		$channeltitleProp->setAccessible(true);
		$channeltitleProp->setValue($video, $channeltitle);

		$thumbnailProp = $refMediaItem->getProperty('_thumbnail');
		$thumbnailProp->setAccessible(true);
		$thumbnailProp->setValue($video, $thumbnail);

		$descriptionProp = $refMediaItem->getProperty('_description');
		$descriptionProp->setAccessible(true);
		$descriptionProp->setValue($video, $description);

		$durationProp = $refMediaItem->getProperty('_duration');
		$durationProp->setAccessible(true);
		$durationProp->setValue($video, $duration);

		$this->assertEquals($id, $video->getId(), "ID not properly returned by MediaItem getter method.");
		$this->assertEquals($title, $video->getTitle(), "Title not properly returned by MediaItem getter method.");
		$this->assertEquals($link, $video->getLink(), "Link not properly returned by MediaItem getter method.");
		$this->assertEquals($thumbsup, $video->getThumbsUp(), "Thumbsup not properly returned by MediaItem getter method.");
		$this->assertEquals($thumbsdown, $video->getThumbsDown(), "Thumbsdown not properly returned by MediaItem getter method.");
		$this->assertEquals($channelid, $video->getChannelId(), "Channel ID not properly returned by MediaItem getter method.");
		$this->assertEquals($channeltitle, $video->getChannelTitle(), "Channel title not properly returned by MediaItem getter method.");
		$this->assertEquals($thumbnail, $video->getThumbnail(), "Thumbnail not properly returned by MediaItem getter method.");
		$this->assertEquals($description, $video->getDescription(), "Description not properly returned by MediaItem getter method.");
		$this->assertEquals($duration, $video->getDuration(), "Duration not properly returned by MediaItem getter method.");
	}
}
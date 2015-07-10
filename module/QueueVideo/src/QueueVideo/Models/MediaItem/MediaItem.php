<?php

namespace QueueVideo\Models\MediaItem;
use QueueVideo\Models\DomainEntityAbstract;
use QueueVideo\Models\DomainEntityInterface;

class MediaItem extends DomainEntityAbstract implements DomainEntityInterface {
	/**
	* @var string
	*/
	private $_link;

    /**
    * @var string
    */
    private $_title;

	/**
	* @var int
	*/
	private $_thumbsup;

	/**
	* @var int
	*/
	private $_thumbsdown;

    /**
    * @var string
    */
    private $_channelid;

    /**
    * @var string
    */
    private $_channeltitle;

    /**
    * @var string
    */
    private $_thumbnail;

    /**
    * @var string
    */
    private $_description;

    /**
    * @var int
    */
    private $_duration;

    /**
     * Sets the value of _link.
     * @param string $_link the _link
     * @return int
     */
    public function setId($_id) {
        $this->_id = $_id;
        return $this;
    }

    /**
     * Gets the value of _title.
     * @return string
     */
    public function getTitle() {
        return $this->_title;
    }

    /**
     * Sets the value of _title.
     * @param string $_title the _title
     * @return MediaItem
     */
    public function setTitle($_title) {
        $this->_title = $_title;
        return $this;
    }

    /**
     * Gets the value of _link.
     * @return string
     */
    public function getLink() {
        return $this->_link;
    }

    /**
     * Sets the value of _link.
     * @param string $_link the _link
     * @return MediaItem
     */
    public function setLink($_link) {
        $this->_link = $_link;
        return $this;
    }

    /**
     * Gets the value of _thumbsup.
     * @return int
     */
    public function getThumbsUp() {
        return $this->_thumbsup;
    }

    /**
     * Sets the value of _thumbsup.
     * @param int $_thumbsup the _thumbsup
     * @return self
     */
    public function setThumbsUp($_thumbsup) {
        $this->_thumbsup = $_thumbsup;
        return $this;
    }

    /**
     * Gets the value of _thumbsdown.
     * @return int
     */
    public function getThumbsDown() {
        return $this->_thumbsdown;
    }

    /**
     * Sets the value of _thumbsdown.
     * @param int $_thumbsdown the _thumbsdown
     * @return self
     */
    public function setThumbsDown($_thumbsdown) {
        $this->_thumbsdown = $_thumbsdown;
        return $this;
    }

    /**
     * Gets the value of _channelid.
     * @return string
     */
    public function getChannelid() {
        return $this->_channelid;
    }

    /**
     * Sets the value of _channelid.
     * @param string $_channelid the _channelid
     * @return self
     */
    public function setChannelid($_channelid) {
        $this->_channelid = $_channelid;
        return $this;
    }

    /**
     * Gets the value of _channeltitle.
     * @return string
     */
    public function getChannelTitle() {
        return $this->_channeltitle;
    }

    /**
     * Sets the value of _channeltitle.
     * @param string $_channeltitle the _channeltitle
     * @return self
     */
    public function setChannelTitle($_channeltitle) {
        $this->_channeltitle = $_channeltitle;
        return $this;
    }

    /**
     * Gets the value of _thumbnail.
     * @return string
     */
    public function getThumbnail() {
        return $this->_thumbnail;
    }

    /**
     * Sets the value of _thumbnail.
     * @param string $_thumbnail the _thumbnail
     * @return self
     */
    public function setThumbnail($_thumbnail) {
        $this->_thumbnail = $_thumbnail;
        return $this;
    }

    /**
     * Gets the value of _description.
     * @return string
     */
    public function getDescription() {
        return $this->_description;
    }

    /**
     * Sets the value of _description.
     * @param string $_description the _description
     * @return self
     */
    public function setDescription($_description) {
        $this->_description = $_description;
        return $this;
    }

    /**
     * Gets the value of _duration.
     * @return int
     */
    public function getDuration() {
        return $this->_duration;
    }

    /**
     * Sets the value of _duration.
     * @param int $_duration the _duration
     * @return self
     */
    public function setDuration($_duration) {
        $this->_duration = $_duration;
        return $this;
    }
}
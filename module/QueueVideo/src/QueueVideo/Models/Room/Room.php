<?php

namespace QueueVideo\Models\Room;

use QueueVideo\Models;

class Room extends Models\DomainEntityAbstract implements Models\DomainEntityInterface {

    /**
    * @var int
    */
    private $_queueid;

	/**
	* @var string
	*/
	private $_password;

    /**
    * @var string
    */
    private $_title;

	/**
	* @var int
	*/
	private $_userlimit;


    /**
     * Sets the value of _password.
     * @param string $_password the _password
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
     * @return Room
     */
    public function setTitle($_title) {
        $this->_title = $_title;
        return $this;
    }

    /**
     * Gets the value of _password.
     * @return string
     */
    public function getPassword() {
        return $this->_password;
    }

    /**
     * Sets the value of _password.
     * @param string $_password the _password
     * @return Room
     */
    public function setPassword($_password) {
        $this->_password = $_password;
        return $this;
    }

    /**
     * Gets the value of _userlimit.
     * @return int
     */
    public function getUserLimit() {
        return $this->_userlimit;
    }

    /**
     * Sets the value of _userlimit.
     * @param int $_userlimit the _userlimit
     * @return self
     */
    public function setUserLimit($_userlimit) {
        $this->_userlimit = $_userlimit;
        return $this;
    }

    /**
     * Gets the value of _queueid.
     * @return int
     */
    public function getQueueId() {
        return $this->_queueid;
    }

    /**
     * Sets the value of _queueid.
     * @param int $_queue_id
     * @return self
     */
    public function setQueueId($_queueid) {
        $this->_queueid = $_queueid;
        return $this;
    }

    
}
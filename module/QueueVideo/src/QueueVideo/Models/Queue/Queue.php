<?php

namespace QueueVideo\Models\Queue;

use QueueVideo\Models;

class Queue extends Models\DomainEntityAbstract implements Models\DomainEntityInterface {
    /**
     * Sets the value of _link.
     * @param string $_link the _link
     * @return int
     */
    public function setId($_id) {
        $this->_id = $_id;
        return $this;
    }
}
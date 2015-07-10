<?php

namespace QueueVideo\Models;

abstract class DomainEntityAbstract {
	protected $_id;

	public function getId() {
		return $this->_id;
	}
}
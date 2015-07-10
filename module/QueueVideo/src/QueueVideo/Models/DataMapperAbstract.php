<?php

namespace QueueVideo\Models;

use Zend\Db\Adapter\Adapter;

abstract class DataMapperAbstract {
	/**
	* @var Zend\Db\Adapter\Adapter
	*/
	protected $_dbCon;

	public function __construct(Adapter $dbAdapter) {
		$this->_dbCon = $dbAdapter;
	}
}
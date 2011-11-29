<?php

class AppModel extends Model {
	var $actsAs = array('Containable');
	var $recursive = -1;

	/*
	var $mongoSchema = array(
		'title' => array('type'=>'string'),
		'body'=>array('type'=>'string'),
		'hoge'=>array('type'=>'string'),
		'created'=>array('type'=>'date'),
		'modified'=>array('type'=>'date'),
	);
	*/

	/*
	 * Executes a MongoDB command
	*/
	function execute($command, $params=array()) {
		$dataSource = ConnectionManager::getDataSource('default');
		$result = $dataSource->execute($command,$params);
		return $result;
	}

	function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
		$doQuery = true;
		// check if we want the cache
		if (!empty($fields['cache'])) {
			$cacheConfig = null;
			// check if we have specified a custom config, e.g. different expiry time
			if (!empty($fields['cacheConfig']))
				$cacheConfig = $fields['cacheConfig'];

			$cacheName = $this->name . '-' . $fields['cache'];

			// if so, check if the cache exists
			if (($data = Cache::read($cacheName, $cacheConfig)) === false) {
				$data = parent::find($conditions, $fields, $order, $recursive);
				Cache::write($cacheName, $data, $cacheConfig);
			}
			$doQuery = false;
		}
		if ($doQuery)
			$data = parent::find($conditions, $fields, $order, $recursive);
		return $data;
	}

	function findById($id) {
		return $this->find('first',array('conditions'=>array('_id'=>$id)));
	}
}
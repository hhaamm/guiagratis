<?php
/*
 * Guia Gratis, sistema para intercambio de regalos.
 * Copyright (C) 2011  Hugo Alberto Massaroli
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
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
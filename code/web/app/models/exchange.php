<?php

class Exchange extends AppModel {
	var $mongoSchema = array(
		'comments'=>array(
			'user_id'=>array('type'=>'integer'),
			'comment'=>array('type'=>'text'),
			'created'=>array('type'=>'timestamp')
		),
		'detail'=>array('type'=>'text'),
		'title'=>array('type'=>'string'),
		'lat'=>array('type'=>'float'),
		'lng'=>array('type'=>'float'),
		'exchange_type_id'=>array('type'=>'integer'),
		'exchange_state_id'=>array('type'=>'integer'),
		'modified'=>array('type'=>'timestamp'),
		'user_id'=>'string',
		'created'=>'integer',
		'state'=>'string',
		'finalize_time'=>'integer',
		'photos'=>array(
			'default', 'id', 'small', 'square'
		)
	);

	function addComment($eid, $comment) {
		$comment = json_encode($comment);
		return $this->execute(new MongoCode(
				"db.exchanges.update({_id:ObjectId('$eid')},{\$push:{comments:$comment}},true,false)"
		));
	}

	function addPhoto($eid, $data, $current_user) {
		$e = $this->findById($eid);

		if ($e['Exchange']['user_id'] != $current_user) {
			$this->log("User ${$current_user} trying to add photos to exchange with id = ${$eid}. Denied");
			return false;
		}

		if (!$e['Exchange']['photos'] || count($e['Exchange']['photos']) == 0) {
			$data['default_photo'] = 1;
		}

		$data = json_encode($data);
		return $this->execute(new MongoCode(
				"db.exchanges.update({_id:ObjectId('$eid')},{\$push:{photos:$data}},true,false)"
		));
	}

	function setDefaultPhoto($eid, $pid, $current_user) {
		$e = $this->findById($eid);

		if ($e['Exchange']['user_id'] != $current_user) {
			$this->log("User ${$current_user} trying to set default photo from exchange with id = ${$eid}. Denied");
			return false;
		}

		foreach ($e['Exchange']['photos'] as &$photo) {
			if ($photo['id'] == $pid) {
				$photo['default'] = 1;
			} else {
				$photo['default'] = 0;
			}
		}
		return $this->save($e);
	}

	function deletePhoto($eid, $pid, $current_user) {
		$e = $this->findById($eid);

		if ($e['Exchange']['user_id'] != $current_user) {
			$this->log("User ${$current_user} trying to delete photos from exchange with id = ${$eid}. Denied");
			return false;
		}

		foreach ($e['Exchange']['photos'] as $photo) {
			if ($photo['id'] == $pid) {

				$photo_data = json_encode($photo);
			}
		}
		if (!isset($photo_data)) {
			debug("Invalid photo");
			return false;
		}
		
		//TODO: hacer que borre también las imágenes
		$query = "db.exchanges.update({_id:ObjectId('$eid')}, {\$pull:{photos:$photo_data}})";
		$result = $this->execute(new MongoCode($query));
		return $result;
	}

	function finalize($eid, $current_user) {
		$exchange = $this->find('first',array('conditions'=>array('_id'=>$eid)));

		if ($exchange['Exchange']['user_id'] != $current_user) {
			$this->log("User ${$current_user} trying to finalize exchange with id = ${$eid}. Denied");
			return false;
		}

		$exchange['Exchange']['state'] = EXCHANGE_FINALIZED;
		$exchange['Exchange']['finalize_time'] = time();
		return $this->save($exchange);
	}
}
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
 * 
 */
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
        'username'=>'string',
		'created'=>'timestamp',
        //TODO: ver si este campo state es necesario
		'state'=>'string',
        //TODO: cambiar a timestamp
		'finalize_time'=>'integer',
		'photos'=>array(
			'default', 'id', 'small', 'square'
		)
	);
    
    var $validate = array(
        'title'=>array(
            'notEmpty'=>array(
                'rule'=>'notEmpty',
                'required'=>true,
                'El título es obligatorio'
            ),
            'between' => array(
                'rule' => array('between', 10, 50),
                'message' => 'Entre 10 y 50 caracteres'
            )
        ),
        'detail'=>array(
            'notEmpty'=>array(
                'rule'=>'notEmpty',
                'required'=>true,
                'La descripción es obligatoria'
            ),
            'between' => array(
                'rule' => array('between', 20, 1000),
                'message' => 'Entre 20 y 1000 caracteres'
            )
        ),
        'user_id'=>'notEmpty',
        'username'=>'notEmpty',
        'exchange_type_id'=>'notEmpty',
        'exchange_state_id'=>'notEmpty',
        'lat'=>'notEmpty',
        'lng'=>'notEmpty'
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
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
    var $catchFinalizedEvents = false;
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
		'finalize_time'=>array('type'=>'timestamp'),
		'photos'=>array(
			'default', 'id', 'small', 'square'
		),
        'tags'=>'string',
        'hours_of_opening'=>'string',
        'start_date'=>array('type'=>'timestamp'),
        'end_date'=>array('type'=>'timestamp'),
		'rates' => array('type'=>'array'),
		'country'=>array('type'=>'string'),
		'province'=>array('type'=>'string'),
		'locality'=>array('type'=>'string')
	);

    var $belongsTo = array('User');
    var $hasMany = array(
        'Comment' => array(
            'className' => 'ExchangeComment',
            'foreignKey' => 'exchange_id'
        ),
        'Photo' => array(
            'className' => 'ExchangePhoto',
            'foreignKey' => 'exchange_id'
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
                'rule' => array('between', 20, 3000),
                'message' => 'Entre 20 y 3000 caracteres'
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
		$this->execute(new MongoCode(
				"db.exchanges.update({_id:ObjectId('$eid')},{\$push:{comments:$comment}},true,false)"
		));
        //TODO: ver cómo hacer para verificar si la ejecución fue correcta.
        return true;
	}

	function setDefaultPhoto($eid, $pid, $current_user) {
		$e = $this->findById($eid);

		if ($e['Exchange']['user_id'] != $current_user) {
			$this->log("User ${$current_user} trying to set default photo from exchange with id = ${$eid}. Denied");
			return false;
		}

        $this->Photo->updateAll(array('is_default' => 0), array('exchange_id' => $eid));
        $this->Photo->id = $pid;
        $this->Photo->save(array('is_default' => 1));
	}

	function deletePhoto($eid, $pid, $current_user) {
		$e = $this->findById($eid);

		if ($e['Exchange']['user_id'] != $current_user) {
			$this->log("User ${$current_user} trying to delete photos from exchange with id = ${$eid}. Denied");
			return false;
		}

		foreach ($e['Exchange']['photos'] as $photo) {
			if ($photo['id'] == $pid) {
                foreach(array('small','square') as $size ){
                    unlink($photo[$size]['file_path']); //borrar del disco
                }
				$photo_data = json_encode($photo);
			}
		}
		if (!isset($photo_data)) {
			debug("Invalid photo");
			return false;
		}
		
		$query = "db.exchanges.update({_id:ObjectId('$eid')}, {\$pull:{photos:$photo_data}})";
		$result = $this->execute(new MongoCode($query));
		return $result;
	}
    
    //devuelve los últimos exchanges en cierto período de tiempo.
    function getLast($timestamp) {
        return $this->find('all', array(
            'conditions'=>array(
                'Exchange.created >' => date("Y-m-d H:i:s", $timestamp)
            ),
            'limit'=>10,
            'order'=>'created DESC'
        ));
    }

	function finalize($exchange) {
		$exchange['Exchange']['state'] = EXCHANGE_FINALIZED;
		$exchange['Exchange']['finalize_time'] = time();
		return $this->save($exchange);
	}
    
    function beforeSave() {
        // TODO: pasar a tabla intermedia
        /*
        $this->data['Exchange']['tags'] = explode(',', $this->data['Exchange']['tags']);
        foreach ($this->data['Exchange']['tags'] as &$tag) {
            $tag = trim($tag);  
        }
        */

        //guardamos la fecha en un formato entendible
        if (isset($this->data['Exchange']['start_date']) && is_array($this->data['Exchange']['start_date'])) {
            $this->data['Exchange']['start_date'] = mktime(
                    $this->data['Exchange']['start_date']['hour'],
                    $this->data['Exchange']['start_date']['min'],
                    $this->data['Exchange']['start_date']['sec'],
                    $this->data['Exchange']['start_date']['month'],
                    $this->data['Exchange']['start_date']['day'],
                    $this->data['Exchange']['start_date']['year']
            );
        }
        if (isset($this->data['Exchange']['end_date']) && is_array($this->data['Exchange']['end_date'])) {
            $this->data['Exchange']['end_date'] = mktime(
                $this->data['Exchange']['end_date']['hour'],
                $this->data['Exchange']['end_date']['min'],
                $this->data['Exchange']['end_date']['sec'],
                $this->data['Exchange']['end_date']['month'],
                $this->data['Exchange']['end_date']['day'],
                $this->data['Exchange']['end_date']['year']
            );
        }
        
        return true;
    }
    
    function afterFind($results, $primary) {
	    if (count($results) == 1 && isset($results[0][0]['count'])) {
		    // la consulta es un count
		    return $results;
	    }

       if($results!=null){
        foreach($results as $key => &$result) {
//            $result['Exchange']['tags'] = implode(', ', $result['Exchange']['tags']);
            
            if ($this->catchFinalizedEvents && $result['Exchange']['exchange_type_id'] == EXCHANGE_EVENT
                    && $result['Exchange']['end_date']->sec < time() && $result['Exchange']['state'] != EXCHANGE_FINALIZED) {
                //TODO: mandar mail de evento finalizado.
                $this->finalize($result);
                unset($results[$key]);
            }
        }
       }
       return $results;
    }

    function removeComment($eid, $i) {
 		$this->execute(new MongoCode(
			"db.exchanges.update({_id:ObjectId('$eid')}, {\$unset : {'comments.$i' : 1 }});db.exchanges.update({_id:ObjectId('$eid')}, {\$pull : {'comments' : null}});"

		));
        return true;
	}

    function getTotalRates($exchange){
        $rates = isset($exchange['Exchange']['rates'])?$exchange['Exchange']['rates']:array();
        $positives = 0; $negatives = 0;
        foreach($rates as $uid =>$rate){
            if($rate){
                $positives++;
            }else{
                $negatives++;
            }
        }
        return array('positives'=>$positives,'negatives'=>$negatives);
    }

}

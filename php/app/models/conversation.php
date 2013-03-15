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

/*
 * Conversation are groups of private messages
 */
class Conversation extends AppModel {
	var $belongsTo = array('User');

	var $mongoSchema = array(
		'from'=>'string',
		'to'=>'string',
		'messages'=>'array',
		'from_new_message'=>'integer',
		'to_new_message'=>'integer',
		'title'=>'string'
	);
    
    var $validate = array(
        'title'=>array(
            'notEmpty'=>array(
                'rule'=>'notEmpty',
                'message'=>'Campo requerido',
                'required'=>true
            ),
            'between' => array(
                'rule' => array('between', 8, 50),
                'message' => 'Entre 8 y 50 caracteres'
            )
        ),
        'to'=>'notEmpty',
        'from'=>'notEmpty',
        'messages'=>array(
            'rule'=>'validateMessage',
            'message'=>'No se puede mandar un mensaje vacío',
            'on'=>'create',
            'required'=>true
        )
    );

    //valida, al crear una conversación, que el mensaje no esté vacío
    function validateMessage($check) {
        $message = $check['messages'][0]['text'];
        
        return !empty($message);
    }
    
	
	/*
	 * Returns current user's conversations
	 */
	function byUser($uid) {
		$toConversations = $this->find('all',array('limit'=>35,'conditions'=>array('to'=>$uid)));
		$fromConversations = $this->find('all',array('limit'=>35,'conditions'=>array('from'=>$uid)));

		if (is_array($toConversations) && is_array($fromConversations)) {
			$conversations = array_merge($toConversations,$fromConversations);
		} elseif (is_array($toConversations)) {
			$conversations = $toConversations;
		} elseif (is_array($fromConversations)) {
			$conversations = $fromConversations;
		} else {
			$conversations = array();
		}
		
		//Adding user names
		foreach ($conversations as &$c) {
			if (isset($c['Conversation']['from'])) {
				$c['Conversation']['from_data'] = $this->User->find('first',array(
					'conditions'=>array('_id'=>$c['Conversation']['from']),
					'cache'=>'user_'.$c['Conversation']['from'],
					'fields'=>array('_id','username')
				));
			}
			if (isset($c['Conversation']['to'])) {
				$c['Conversation']['to_data'] = $this->User->find('first',array(
					'conditions'=>array('_id'=>$c['Conversation']['to']),
					'cache'=>'user_'.$c['Conversation']['to'],
					'fields'=>array('_id','username')
				));
			}
		}
		return $conversations;
	}

	function answer($cid, $answer, $current_user) {
		$c = $this->findById($cid);

		if ($c['Conversation']['from'] != $current_user && $c['Conversation']['to'] != $current_user) {
			log("User ${$current_user} trying to answer to conversation with id = ${$cid}");
			return false;
		}

		$answer = json_encode($answer);
		return $this->execute(new MongoCode(
				"db.conversations.update({_id:ObjectId('$cid')},{\$push:{messages:$answer}},true,false)"
		));
	}

	function view($cid, $current_user) {
		$c = $this->findById($cid);

		if ($c['Conversation']['from'] != $current_user && $c['Conversation']['to'] != $current_user) {
			log("User $current_user trying to see conversation with id = $cid");
			return false;
		}

		foreach($c['Conversation']['messages'] as &$message) {
			$from = $message['from'];

			$from_data = $this->User->find('first',array(
				'conditions'=>array('_id'=>$from),
				'cache'=>'user_'.$from
			));
			$message['from_data']=$from_data['User'];
		}

		return $c;
	}
}
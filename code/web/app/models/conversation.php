<?php

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
			log("User ${$current_user} trying to see conversation with id = ${$cid}");
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
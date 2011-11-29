<?php

class ConversationsController extends AppController {
	function add($to = null) {
		if ($this->data) {
			$this->data['Conversation']['from'] = $this->Auth->user('_id');
			$this->data['Conversation']['from_new_message']=0;
			$this->data['Conversation']['to_new_message']=1;
			$this->data['Conversation']['messages'][0]['created'] = time();
			$this->data['Conversation']['messages'][0]['from'] = $this->Auth->user('_id');

			$this->Conversation->save($this->data);
			$this->Session->setFlash('Mensaje enviado');
			$this->redirect('/conversations/view/'.$this->Conversation->id);
		}

		if (!$to) {
			debug("Missing to parameter");
			$this->getBack();
		}
		$this->data['Conversation']['to']=$to;
	}

	function index() {
		$conversations = $this->Conversation->byUser($this->uid);
		
		$this->set(compact('conversations'));
	}

	function view($cid) {
		$conversation = $this->Conversation->view($cid, $this->uid);
		$this->set(compact('conversation'));
	}

	function answer() {
		$answer = array(
			'from'=>$this->Auth->user('_id'),
			'created'=>time(),
			'text'=>$this->data['Conversation']['text']
		);
		$result = $this->Conversation->answer($this->data['Conversation']['_id'],$answer, $this->uid);
		$this->getBack('Mensaje enviado');
	}
}
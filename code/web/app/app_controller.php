<?php

class AppController extends Controller {
	var $components = array('Session','Auth','Email');
	var $helpers = array('Javascript','Html','Session','Form','Time');
	var $uid;

	function beforeFilter() {
        $this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');
        $this->Auth->loginError = "El usuario o la contraseña son incorrectos";
        $this->Auth->autoRedirect = true;
		$this->Auth->userScope = array('User.active'=>1);
		$this->Auth->fields = array('username' => 'mail', 'password' => 'password');
        
        $this->uid = $this->Auth->user('_id');
        
		$this->set('current_user', $this->Auth->user());
    }

	protected function sendMail($to, $subject, $template, $opts = array()) {
		$this->Email->sendAs = 'html';
		$this->Email->to = $to;
		$this->Email->subject = $subject;
		$this->Email->template = $template;
		$this->Email->from = Configure::read('Mail.from');
		$this->Email->send($opts);
	}

	protected function result($result, $message = '', $data = array()) {
		$this->autoRender = false;
		$data['result'] = $result;
		$data['message'] = $message;
		echo json_encode($data);
		exit();
	}

	protected function require_fields($array,$fields) {
		foreach ($fields as $field) {
			if (!isset($array[$field]))
				return "Missing field '$field'";
		}
		return true;
	}

	protected function getBack($message = null) {
		if ($message)
			$this->Session->setFlash($message);
		$this->redirect($this->referer());
	}
}
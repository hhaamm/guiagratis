<?php

class User extends AppModel {
	var $primaryKey = '_id';

	var $mongoSchema = array(
		'mail' => array('type'=>'string'),
		'first_name'=>array('type'=>'string'),
		'last_name'=>array('type'=>'string'),
		'username'=>array('type'=>'string'),
		'created'=>array('type'=>'timestamp'),
		'modified'=>array('type'=>'timestamp'),
		'password'=>array('type'=>'string'),
		'active'=>array('type'=>'integer'),
		'register_token'=>array('type'=>'string'),
		'admin'=>array('type'=>'integer'),
		'reset_password_token'=>array('type'=>'integer')
	);

	function mail_already_registered($mail) {
		$user = $this->find('first',array('conditions'=>compact('mail')));
		return !empty($user);
	}

	function user_already_registered($username) {
		$user = $this->find('first',array('conditions'=>compact('username')));
		return !empty($user);
	}
}
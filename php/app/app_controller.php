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
class AppController extends Controller {
	var $components = array('Session','Auth','Email');
	var $helpers = array('Javascript','Html','Session','Form','Time', 'User', 'UserWorkaround');
    var $models = array('User');
	var $uid;

	function beforeFilter() {
	        $this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');
	        $this->Auth->loginError = "El usuario o la contraseña son incorrectos";
        	$this->Auth->autoRedirect = true;
		$this->Auth->fields = array('username' => 'email', 'password' => 'password');

		//el usuario debe ser activo y no debe ser del tipo facebook (para evitar ataques al 'facebook_password')
		//para loguearse por vías "normales"
		$this->Auth->userScope = array('User.active'=>1, 'User.facebook_id'=>null);

		$user_refresh = $this->Session->read('Auth.User.refresh_time');
		if( $this->Auth->user('id') && ( !$user_refresh ||   time() - $user_refresh  > 60)){
          //refresca el usuario cada 1 minuto para que se vean las actualizaciones.
          //TODO hacer que el tiempo sea configurable
          $this->refreshCurrentUser();
          $this->Session->write('Auth.User.refresh_time',time());
        }
        
        $this->uid = $this->Auth->user('id');
        
        //check if the user is an admin and an admin route was requested 
        if(isset($this->params['admin']) && $this->params['admin']) {  
            // check user is logged in  
            if(!$this->Auth->user('admin')) { 
                //TODO: log warning. Attempt to enter to admin section.
                $this->redirect('/');  
            }   
        }  
        $this->set('is_admin', $this->Auth->user('admin'));
		$this->set('current_user', $this->Auth->user());
        $this->set('title_for_layout', '¿Necesitás algo? Conseguilo en guia-gratis.com.ar');
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

	protected function getBack($message = null,$element = 'default') {
		if ($message)
			$this->Session->setFlash($message,$element);
		$this->redirect($this->referer());
	}

    function refreshCurrentUser(){
      if($this->Auth->user('id')){
        $user = $this->User->read(null, $this->Auth->user('id'));
        $this->Session->write('Auth.User',$user['User']);
      }
    }
}
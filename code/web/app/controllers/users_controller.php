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
class UsersController extends AppController {

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('register','login', 'logout', 'create', 'modal_login','modal_register','validate_mail','validate_username','verify','forgot_password','reset','activate');
	}

	function login() {
		if ($this->data && !$this->Auth->user()) {
			$this->Session->setFlash('Usuario o contraseña inválidos');
		}
	}

	function logout() {
		$this->redirect($this->Auth->logout());
	}

	function register() {
		if ($this->data) {
			if ($this->data['User']['terms']) {
				if ($this->data['User']['password'] != $this->Auth->password($this->data['User']['confirm_password'])) {
					$this->Session->setFlash('Las contraseñas tienen que ser iguales');
				} else {
					if ($this->User->user_already_registered($this->data['User']['username'])) {
						$this->Session->setFlash('El usuario ya existe!');
						return;
					}

					$user = $this->User->mail_already_registered($this->data['User']['mail']);
					if (!empty($user)) {
						$this->Session->setFlash('Ese mail ya fue registrado!');
						return;
					}

					$hash = sha1($this->data['User']['username'].rand(0,100));
					$this->data['User']['register_token'] = $hash;
					$this->set('registration_link', Configure::read('Host.url')."users/activate/$hash");
					$this->data['User']['active'] = 0;
					$this->data['User']['admin'] = 0;

					//Sending mail
					$this->sendMail($this->data['User']['mail'],"Confirmá tu registración",'activate_account');

					//Register user
					$this->User->save($this->data);
					$this->Session->setFlash('Enviamos un mail a tu casilla de correo para terminar el registro.');
					$this->redirect('/');
				}
			} else {
				$this->Session->setFlash('Tenés que aceptar los Términos y Condiciones para registrarte');
				$this->data['User']['password'] = '';
				$this->data['User']['confirm_password'] = '';
			}
		}
	}

	function activate($token) {
		if ($this->User->updateAll(array('active' => 1), array('User.register_token' => $token))) {
			$user = $this->User->find('first', array('conditions'=>array('register_token'=>$token)));
			
			if (!empty($user)) {
				$this->sendMail($user['User']['mail'],'¡Bienvenido!','welcome');

				$this->Session->setFlash('La cuenta fue activada. ¡Ya podés loguearte!');
				
			}
		} else {
            $this->Session->setFlash('Información equivocada');
        }
        $this->redirect('/');
	}

	function account() {
		$this->set('user', $this->User->findById($this->Auth->user('id')));
		$currentModReq = $this->ModeratorshipRequest->find('first', array('conditions' => array('ModeratorshipRequest.user_id' => $this->uid, 'closed' => 0)));
		$currentModReq= empty($currentModReq) ? 1 : 0;
		$this->set('currentModReq',$currentModReq);
	}

	function change_password() {
		if ($this->data) {
			$uid = $this->Auth->user('id');
			$p = $this->data['User']['new_password'];
			if ($p != $this->data['User']['confirm_password']) {
				$this->Session->setFlash("Passwords doesn't match");
				$this->redirect('account');
				exit();
			}
			$db_password = $this->User->getPassword($uid);
			if ($this->Auth->password($this->data['User']['old_password']) != $db_password) {
				$this->Session->setFlash("Invalid old password");
				$this->redirect('account');
				exit();
			}
			$this->User->id = $uid;
			$this->User->saveField('password', $this->Auth->password($p));
			$this->Session->setFlash('Password changed successfully');
		}
		$this->redirect('account');
	}

	function delete() {
		$p = $this->Auth->password($this->data['User']['password']);
		$db_password = $this->User->getPassword($this->uid);
		if ($p == $db_password) {
			$this->User->id = $this->uid;
			$this->User->saveField('state', Configure::read('UserState.Deleted'));
			$this->Session->setFlash('Your account has been deleted');
			$this->redirect($this->Auth->logout());
		} else {
			$this->Session->setFlash('Wrong password');
			$this->redirect('account');
		}
	}

	function profile($uid) {
		$this->populateUsersProfile($uid);
	}

	function validate_username() {
		$this->autoRender = false;
		$username = $_REQUEST['data']['User']['username'];
		$user = $this->User->findByUsername($username);
		if (empty($user))
			echo json_encode(true);
		else
			echo json_encode(false);
	}

	function validate_mail() {
		$this->autoRender = false;
		$mail = $_REQUEST['data']['User']['mail'];
		$user = $this->User->findByMail($mail);
		if (empty($user))
			echo json_encode(true);
		else
			echo json_encode(false);
	}

	//Used for sms verification
	function verify() {
		$cellphone = isset($_REQUEST['cellphone']) ? $_REQUEST['cellphone'] : $this->Session->read('Verify.Cellphone');
		if (isset($_REQUEST['cellphone'])) {
			//TODO: handle here multiple sms hack!

			$this->Session->write('Verify.Cellphone', $cellphone);
			$token = substr(uniqid(), 0, 5);
			$data = array('verify_token' => $token, 'id' => $this->Auth->user('id'));
			$this->User->save($data);

			$result = $this->Sms->sendVerificationCode($token, $cellphone);

			if (!$result) {
				$this->redirectExit('/users/verify', 'An error has ocurred sending the token');
			}

			$this->set('cellphone', $cellphone);
			$this->set('action', 'token');
		} else {
			$this->set('action', 'cellphone');
		}
	}

	/*
     * Returns users json encoded
	*/
	function get() {
		$this->autoRender = false;
		$query = $_REQUEST['search'];
		//TODO: put this in a cake find
		$sql = "SELECT id, username FROM users WHERE username LIKE '$query%' AND state > 0 AND (user_ban_end_time < '".timestamp()."' OR user_ban_end_time IS NULL)";
		if (isset($_REQUEST['excluded_ids'])) {
			$excluded_ids = $_REQUEST['excluded_ids'];
			$sql .= " AND id NOT IN ($excluded_ids)";
		}

		$users = $this->User->query($sql);
		$response = array();
		foreach ($users as $user) {
			$response[] = array($user['users']['id'], $user['users']['username'], null, null);
		}
		echo json_encode($response);
	}

	public function forgot_password() {
		if ($this->data) {
			$mail = $this->data['User']['mail'];
			$user = $this->User->find('first',array(
				'conditions'=>array(
					'mail'=>$mail
				)
			));

			if (empty($user)) {
				$this->Session->setFlash('Mal inválido.');
				return;
			}

			$code = rand(1000, 9999);
			$data = array('User'=>array(
				'reset_password_token'=>$code,
				'_id'=>$user['User']['_id']
			));
			
			if ($this->User->save($data)) {
				$this->set('code', $code);
				$this->sendMail($mail,'Reseteo de contraseña','reset_password');
				$this->redirect('reset');
			} else {
				$this->Session->setFlash('Ha ocurrido un error');
			}
		}
	}

	public function reset() {
		if ($this->data) {
			if ($this->data['User']['password'] != $this->Auth->password($this->data['User']['confirm_password'])) {
				$this->Session->setFlash("Las contraseñas no son iguales");
				return;
			}

			$user = $this->User->find('first', array('conditions' => array(
							'User.mail' => $this->data['User']['mail'],
							'User.reset_password_token' => (int)$this->data['User']['reset_password_code']
			)));

			if (empty($user)) {
				$this->Session->setFlash('Email o token inválidos');
				return;
			}

			$this->User->id = $user['User']['_id'];
			if ($this->User->saveField('password', $this->data['User']['password'])) {
				$this->Session->setFlash('Contraseña reseteada. Ya puede loguearse');
				$this->redirect('/');
			} else {
				$this->Session->setFlash('Ocurrió un error.');
			}
		}
		unset($this->data['User']['password']);
		unset($this->data['User']['confirm_password']);
	}
}
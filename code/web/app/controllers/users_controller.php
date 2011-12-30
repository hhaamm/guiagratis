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

    var $uses = array('Exchange','User');
    var $helpers = array('Exchange');
    var $components = array('Upload');



	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('register','login', 'logout', 'create', 'modal_login','modal_register','validate_mail','validate_username','verify','forgot_password','reset','activate','view');
	}

	function login() {
		if ($this->data && !$this->Auth->user()) {
			$this->Session->setFlash('Usuario o contraseña inválidos','flash_failure');
		}
	}

	function logout() {
		$this->redirect($this->Auth->logout());
	}

    function view($id){
        $user = $this->User->findById($id);
        $exchanges = $this->Exchange->find('all',array(
			'conditions'=>array('user_id'=>$user['User']['_id']),
			'limit'=>35
		));
        $this->set(compact('user','exchanges'));

    }

	function register() {
		if ($this->data) {
			if ($this->data['User']['terms']) {
				if ($this->data['User']['password'] != $this->Auth->password($this->data['User']['confirm_password'])) {
					$this->Session->setFlash('Las contraseñas tienen que ser iguales','flash_failure');
				} else {
					if ($this->User->user_already_registered($this->data['User']['username'])) {
						$this->Session->setFlash('El usuario ya existe!','flash_failure');
						return;
					}

					$user = $this->User->mail_already_registered($this->data['User']['mail']);
					if (!empty($user)) {
						$this->Session->setFlash('Ese mail ya fue registrado!','flash_failure');
						return;
					}

					$hash = sha1($this->data['User']['username'].rand(0,100));
					$this->set('registration_link', Configure::read('Host.url')."users/activate/$hash");
                    $this->data['User'] = array_merge( $this->data['User'], array(
                        'active'=>0,
                        'admin'=>0,
                        'register_token'=>$hash,
                        'notify_on_message'=>true,
                        'notify_on_answer'=>true
                    ));

					//Register user
					if ($this->User->save($this->data)) {
                        //Sending mail
                        $this->sendMail($this->data['User']['mail'],"Confirmá tu registración",'activate_account');
                        $this->Session->setFlash('Enviamos un mail a tu casilla de correo para terminar el registro. Si no te llegó, es posible que haya quedado en la carpeta de SPAM / CORREO NO DESEADO.');
                        $this->redirect('/');
                    }
				}
			} else {
				$this->Session->setFlash('Tenés que aceptar los Términos y Condiciones para registrarte','flash_warning');
				$this->data['User']['password'] = '';
				$this->data['User']['confirm_password'] = '';
			}
            //si falló el registro ponemos de vuelta la password original del usuario (no la convertida por CakePHP)
            $this->data['User']['password'] = $this->data['User']['confirm_password'];
		}
	}

    function edit_profile(){
        $user = $this->User->read(null, $this->Auth->user('_id'));

        if ($this->data) {
            $this->User->set($user);
            if($this->User->save($this->data)){
                $this->redirect(array('action'=>'view',$this->Auth->user('_id')));
            }else{
                //var_dump($this->User->invalidFields());
				$this->Session->setFlash('Hubo un error al guardar tus datos');
            }
        }else{
            $this->data = $user;
        }
    }

    function change_avatar(){
        $this->autoLayout = false;
        if($this->data['Photo']['id'] != $this->Auth->user('_id')){
            return;
        }
        $uid = $this->data['Photo']['id'];
        $result = $this->Upload->images(array('images' => array(
            'small' => array('width' => 50, 'height' => 50, 'keep_aspect_ratio' => true),
            'medium' => array('width' => 100,'height' => 100, 'keep_aspect_ratio' => true),
            'large' => array('width' => 150,'height' => 150, 'keep_aspect_ratio' => true)
        ),
            'dest_path' => WWW_ROOT.'uploads',
            'file_field'=>'photo'));
        $image =  array('id'=>uniqid(null, true),'small'=>$result['small'],'medium'=>$result['medium'],'large'=>$result['large']);
        $this->User->setAvatar($image,$uid);
        $img_url = $result['medium']['url'];
        $user =  $this->User->read(null, $this->Auth->user('_id'));
        $_SESSION['Auth']['User'] = $user['User']; //actualizar la url del avatar en la secio
        $this->set(compact('img_url'));
    }

	function activate($token) {
		if ($this->User->updateAll(array('active' => 1), array('User.register_token' => $token))) {
			$user = $this->User->find('first', array('conditions'=>array('register_token'=>$token)));
			
			if (!empty($user)) {
				$this->sendMail($user['User']['mail'],'¡Bienvenido!','welcome');

				$this->Session->setFlash('La cuenta fue activada. ¡Ya podés loguearte!','flash_success');
				
			}
		} else {
            $this->Session->setFlash('Información equivocada','flash_failure');
        }
        $this->redirect('/');
	}

	function account() {
        if ($this->data) {
            //TODO: forma bastante fea de hacer un update. Mejorar?
            $user = $this->User->findById($this->Auth->user('_id'));
            $user = array_merge($user['User'], $this->data['User']);
            if ($this->User->save($user)) {
                $this->Session->setFlash('Configuración guardada');
            } else {
                $this->Session->setFlash('Hubo un problema al guardar la configuración');
            }       
        }
        //trae la información del usuario mas actualizada de la base de datos
        $this->data = $this->User->findById($this->Auth->user('_id'));
	}

	function change_password() {
		if ($this->data) {
			$uid = $this->Auth->user('id');
			$p = $this->data['User']['new_password'];
			if ($p != $this->data['User']['confirm_password']) {
                $this->Session->setFlash('Las contraseñas tienen que ser iguales','flash_failure');
				$this->redirect('account');
				exit();
			}
			$db_password = $this->User->getPassword($uid);
			if ($this->Auth->password($this->data['User']['old_password']) != $db_password) {
				$this->Session->setFlash("Tu antigua contraseña no es la que has ingresado",'flash_failure');
				$this->redirect('account');
				exit();
			}
			$this->User->id = $uid;
			$this->User->saveField('password', $this->Auth->password($p));
			$this->Session->setFlash('Tu password a sido cambiado con exito','flash_success');
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
				$this->Session->setFlash('Mal inválido.','flash_failure');
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
				$this->Session->setFlash('Ha ocurrido un error','flash_failure');
			}
		}
	}

	public function reset() {
		if ($this->data) {
			if ($this->data['User']['password'] != $this->Auth->password($this->data['User']['confirm_password'])) {
				$this->Session->setFlash("Las contraseñas no son iguales",'flash_failure');
				return;
			}

			$user = $this->User->find('first', array('conditions' => array(
							'User.mail' => $this->data['User']['mail'],
							'User.reset_password_token' => (int)$this->data['User']['reset_password_code']
			)));

			if (empty($user)) {
				$this->Session->setFlash('Email o token inválidos','flash_failure');
				return;
			}

			$this->User->id = $user['User']['_id'];
			if ($this->User->saveField('password', $this->data['User']['password'])) {
				$this->Session->setFlash('Contraseña reseteada. Ya puede loguearse','flash_success');
				$this->redirect('/');
			} else {
				$this->Session->setFlash('Ocurrió un error.','flash_success');
			}
		}
		unset($this->data['User']['password']);
		unset($this->data['User']['confirm_password']);
	}
    
    // admin methods
    function admin_index() {
        $count = $this->User->find('count');
        $countActive = $this->User->find('count', array(
            'conditions'=>array(
                'active'=>1
            )
        ));
        $countInactive = $this->User->find('count', array(
            'conditions'=>array(
                'active'=>0
            )
        ));
        $countAdmin = $this->User->find('count', array(
            'conditions'=>array(
                'active'=>1,
                'admin'=>1
            )
        ));
        $inactivePercentage = round($countInactive/$count*100, 2);
        $activePercentage = 100 - $inactivePercentage;
        $users = $this->User->find('all', array('limit'=>500));
        $this->set(compact('users', 'count', 'countActive', 'countInactive', 'countAdmin', 'inactivePercentage', 'activePercentage'));
    }

    function notifications(){
       $user = $this->Session->read('Auth.User');
       $notifications = isSet($user['notifications']) ? $user['notifications'] : array();
       $there_are_unread = false;
       foreach($notifications as $i => $notification){
           if(!$notification['has_been_read']){
            $there_are_unread = true;
            $this->Session->write("Auth.User.notifications.$i.has_been_read",1);
           }
       }
       if($there_are_unread){
        $this->User->updateNotifications($this->Auth->user('_id'),$this->Session->read('Auth.User.notifications'));
       }
       $notifications = array_reverse($notifications);
       $this->set(compact('notifications'));
    }

}
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
    var $uses = array('Exchange', 'User');
    var $helpers = array('Exchange');
    var $components = array('Upload', 'Image');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('register', 'login', 'logout', 'create', 'modal_login', 'modal_register', 'validate_mail', 'validate_username', 'verify', 'forgot_password', 'reset', 'activate', 'view', 'facebook_login', 'contact');
    }

    function login() {
        if ($this->data && !$this->Auth->user()) {
            $this->Session->setFlash('Usuario o contraseña inválidos', 'flash_failure');
        }
    }

    function facebook_login() {
	//facebook auth
	App::import('Vendor', 'facebook/facebook');
	$facebook = new Facebook(array(
      		'appId'=>Configure::read('Facebook.app_id'),
		'secret'=>Configure::read('Facebook.app_secret'),
		'fileUpload'=>false
	));

	if ($facebook->getUser() && !$this->Auth->user()) {
	    // TODO: implementar dentro del if inferior...
	    // bajamos la foto de perfil de Facebook
	    // $photo = file_get_contents('http://graph.facebook.com/'.$facebook->getUser().'/picture');
	    // var_dump($photo);die();

	  $user = $this->User->find('first', array('conditions'=>array('facebook_id'=>$facebook->getUser())));
	  if (!$user) {
	    //creamos el usuario
	    $data = $facebook->api('/me');
	    $username = str_replace(' ', '_', 'fb'.$facebook->getUser().'_'.strtolower($data['first_name']).'_'.strtolower($data['last_name']));
	    $user_data = array(
		       'firstname'=>$data['first_name'],
		       'lastname'=>$data['last_name'],
		       'facebook_id'=>$data['id'],
		       // construimos un nombre de usuario con el id de Facebook, el nombre y el apellido
		       'username'=>$username,
		       //crearle una password cualquiera
		       'password'=>$this->Auth->password('dummy'),
		       'email'=>empty($data['email']) ? 'no_email_from_facebook' : $data['email'],
		       'active'=>1,
               'admin' => 0
      	     );
	    $this->User->create();
	    $user = $this->User->save($user_data);
	  }

	  //logueamos al usuario "a mano"
	  $this->Session->write('Auth.User', $user['User']);
	  $this->redirect('/');
	} else {
	  $this->Session->flash('¿Estás seguro que estás logueado a Facebook?');
	  $this->redirect('/users/login');
	}
    }

    function logout() {
        $this->redirect($this->Auth->logout());
    }

    function view($id) {
        $user = $this->User->findById($id);

        if (!$user || $user['User']['active'] != 1) {
            $this->cakeError('error404');
            // TODO: ver si se puede dar un mensaje custom:
            // 'El perfil que estás buscando no existe o fue borrado'
        }

        $this->Exchange->contain('Photo');
        $exchanges = $this->Exchange->find('all', array(
            'conditions' => array('user_id' => $user['User']['id']),
            'limit' => 35
        ));
        $this->set(compact('user', 'exchanges'));
    }

    function register() {
        if ($this->data) {
            if ($this->data['User']['terms']) {
                if ($this->data['User']['password'] != $this->Auth->password($this->data['User']['confirm_password'])) {
                    $this->Session->setFlash('Las contraseñas tienen que ser iguales', 'flash_failure');
                } else {
                    if ($this->User->user_already_registered($this->data['User']['username'])) {
                        $this->Session->setFlash('El usuario ya existe!', 'flash_failure');
                        return;
                    }

                    $user = $this->User->email_already_registered($this->data['User']['email']);
                    if (!empty($user)) {
                        $this->Session->setFlash('Ese email ya fue registrado!', 'flash_failure');
                        return;
                    }

                    $hash = sha1($this->data['User']['username'] . rand(0, 100));
                    $this->set('registration_link', Configure::read('Host.url') . "users/activate/$hash");
                    $this->data['User'] = array_merge($this->data['User'], array(
                        'active' => 0,
                        'admin' => 0,
                        'register_token' => $hash,
                        'notify_on_message' => true,
                        'notify_on_answer' => true
                            ));

                    //Register user
                    if ($this->User->save($this->data)) {
                        //Sending mail
                        $this->sendMail($this->data['User']['email'], "Confirmá tu registración", 'activate_account');
                        $this->Session->setFlash('Enviamos un mail a tu casilla de correo para terminar el registro. Si no te llegó, es posible que haya quedado en la carpeta de SPAM / CORREO NO DESEADO.', 'flash_success');
                        $this->redirect('/');
                    } else {
                        foreach ($this->User->validationErrors as $field => $error) {
                            $this->Session->setFlash($field.": ".$error, 'flash_failure');
                        }
                    }
                }
            } else {
                $this->Session->setFlash('Tenés que aceptar los Términos y Condiciones para registrarte', 'flash_warning');
                $this->data['User']['password'] = '';
                $this->data['User']['confirm_password'] = '';
            }
            //si falló el registro ponemos de vuelta la password original del usuario (no la convertida por CakePHP)
            $this->data['User']['password'] = $this->data['User']['confirm_password'];
        }
    }

    function edit_profile() {
        $user = $this->User->read(null, $this->Auth->user('id'));

        if ($this->data) {
            $this->User->set($user);
            if ($this->User->save($this->data)) {
                $this->redirect(array('action' => 'view', $this->Auth->user('id')));
            } else {
                //var_dump($this->User->invalidFields());
                $this->Session->setFlash('Hubo un error al guardar tus datos');
            }
        } else {
            $this->data = $user;
        }
    }

    function change_avatar() {
        $this->autoLayout = false;
        if ($this->data['Photo']['id'] != $this->Auth->user('id')) {
            return;
        }
        $uid = $this->data['Photo']['id'];       
        
        //move_uploaded_file
        $img_id = uniqid(null, true);
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        // TODO: check they are not uploading .exe, .sh, etc. files
        $filename = $img_id.'.'.$ext;
        $filepath = WWW_ROOT.'uploads/'.$filename;
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $filepath)) {
            die("Error al subir la imágen");
        }

        // TODO: move all this code to model
        // TODO: put all this config in core.php
        $square_filepath = WWW_ROOT.'uploads/'.$img_id.'_square.'.$ext;
        $medium_square_filepath = WWW_ROOT.'uploads/'.$img_id.'_medium_square.'.$ext;
        $large_square_filepath = WWW_ROOT.'uploads/'.$img_id.'_large_square.'.$ext;
        copy($filepath, $square_filepath);
        copy($filepath, $medium_square_filepath);
        copy($filepath, $large_square_filepath);
        // TODO: keep aspect ratio but make widht and hegiht the same
        $this->Image->resizeImg($square_filepath, 50); // TODO: keep aspect ratio
        $this->Image->resizeImg($medium_square_filepath, 100); // TODO: keep aspect ratio
        $this->Image->resizeImg($large_square_filepath, 150); // TODO: keep aspect ratio

        $this->User->setAvatar($this->Auth->user('id'), $filename);
        $user = $this->User->read(null, $this->Auth->user('id'));
        $_SESSION['Auth']['User'] = $user['User']; //actualizar la url del avatar en la sección
        $this->set('img_url', '/uploads/'.$img_id.'_medium_square.'.$ext);
    }

    function activate($token) {
        if ($this->User->updateAll(array('active' => 1), array('User.register_token' => $token))) {
            $user = $this->User->find('first', array('conditions' => array('register_token' => $token)));

            if (!empty($user)) {
                $this->sendMail($user['User']['mail'], '¡Bienvenido!', 'welcome');

                $this->Session->setFlash('La cuenta fue activada. ¡Ya podés loguearte!', 'flash_success');
            }
        } else {
            $this->Session->setFlash('Información equivocada', 'flash_failure');
        }
        $this->redirect('/');
    }

    function account() {
        if ($this->data) {
            //TODO: forma bastante fea de hacer un update. Mejorar?
            $user = $this->User->findById($this->Auth->user('id'));
            $user = array_merge($user['User'], $this->data['User']);
            if ($this->User->save($user)) {
                $this->Session->setFlash('Configuración guardada');
            } else {
                $this->Session->setFlash('Hubo un problema al guardar la configuración');
            }
        }
        //trae la información del usuario mas actualizada de la base de datos
        $this->data = $this->User->findById($this->Auth->user('id'));
    }

    function change_password() {
        if ($this->data) {
            $uid = $this->Auth->user('id');
            $p = $this->data['User']['new_password'];
            if ($p != $this->data['User']['confirm_password']) {
                $this->Session->setFlash('Las contraseñas tienen que ser iguales', 'flash_failure');
                $this->redirect('account');
                exit();
            }
            $db_password = $this->User->getPassword($uid);
            if ($this->Auth->password($this->data['User']['old_password']) != $db_password) {
                $this->Session->setFlash("Tu antigua contraseña no es la que has ingresado", 'flash_failure');
                $this->redirect('account');
                exit();
            }
            $this->User->id = $uid;
            $this->User->saveField('password', $this->Auth->password($p));
            $this->Session->setFlash('Tu password a sido cambiado con exito', 'flash_success');
        }
        $this->redirect('account');
    }

    function delete() {
        $p = $this->Auth->password($this->data['User']['password']);
        $db_password = $this->User->getPassword($this->uid);

        if ($p == $db_password) {
            $this->User->id = $this->uid;
            // -1 is when a user has been deleted
            // TODO: move that number to config
            $this->User->saveField('active', -1); 
            $this->Session->setFlash('Tu cuenta ha sido borrada');
            $this->redirect($this->Auth->logout());
        } else {
            $this->Session->setFlash('Password equivocada');
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
        $sql = "SELECT id, username FROM users WHERE username LIKE '$query%' AND state > 0 AND (user_ban_end_time < '" . timestamp() . "' OR user_ban_end_time IS NULL)";
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
            $user = $this->User->find('first', array(
                        'conditions' => array(
                            'mail' => $mail
                        )
                    ));

            if (empty($user)) {
                $this->Session->setFlash('Mal inválido.', 'flash_failure');
                return;
            }

            $code = rand(1000, 9999);
            
            $this->User->id = $user['User']['id'];
            if ($this->User->saveField('reset_password_token', $code)) {
                $this->set('code', $code);
                $this->sendMail($mail, 'Reseteo de contraseña', 'reset_password');
                $this->redirect('reset');
            } else {
                $this->Session->setFlash('Ha ocurrido un error', 'flash_failure');
            }
        }
    }

    public function reset() {
        if ($this->data) {
            if ($this->data['User']['password'] != $this->Auth->password($this->data['User']['confirm_password'])) {
                $this->Session->setFlash("Las contraseñas no son iguales", 'flash_failure');
                return;
            }

            $user = $this->User->find('first', array('conditions' => array(
                            'User.mail' => $this->data['User']['mail'],
                            'User.reset_password_token' => (int) $this->data['User']['reset_password_code']
                            )));

            if (empty($user)) {
                $this->Session->setFlash('Email o token inválidos', 'flash_failure');
                return;
            }

            $this->User->id = $user['User']['id'];
            if ($this->User->saveField('password', $this->data['User']['password'])) {
                $this->Session->setFlash('Contraseña reseteada. Ya puede loguearse', 'flash_success');
                $this->redirect('/');
            } else {
                $this->Session->setFlash('Ocurrió un error.', 'flash_success');
            }
        }
        unset($this->data['User']['password']);
        unset($this->data['User']['confirm_password']);
    }

    // admin methods
    function admin_index() {
        $count = $this->User->find('count');
        $countActive = $this->User->find('count', array(
                    'conditions' => array(
                        'active' => 1
                    )
                ));
        $countInactive = $this->User->find('count', array(
                    'conditions' => array(
                        'active' => 0
                    )
                ));
        $countAdmin = $this->User->find('count', array(
                    'conditions' => array(
                        'active' => 1,
                        'admin' => 1
                    )
                ));
        $inactivePercentage = round($countInactive / $count * 100, 2);
        $activePercentage = 100 - $inactivePercentage;
        $users = $this->User->find('all', array('limit' => 500));
        $this->set(compact('users', 'count', 'countActive', 'countInactive', 'countAdmin', 'inactivePercentage', 'activePercentage'));
    }
    
    function admin_view($id) {
        $user = $this->User->findById($id);
        $this->set(compact('user'));
    }

    function notifications() {
        $user = $this->Session->read('Auth.User');
        $notifications = isSet($user['notifications']) ? $user['notifications'] : array();
        $there_are_unread = false;
        foreach ($notifications as $i => $notification) {
            if (!$notification['has_been_read']) {
                $there_are_unread = true;
                $this->Session->write("Auth.User.notifications.$i.has_been_read", 1);
            }
        }
        if ($there_are_unread) {
            $this->User->updateNotifications($this->Auth->user('id'), $this->Session->read('Auth.User.notifications'));
        }
        $notifications = array_reverse($notifications);
        $this->set(compact('notifications'));
    }

    function contact() {
        if ($this->data) {
            $this->sendMail('Mail.complaint', 'Contacto de usuario', 'user_contact', array('user_email' => $this->data['Contact']['email'], 'user_message' => $this->data['Contact']['message']));
            $this->Session->setFlash('Gracias por tu consulta. Responderemos en breve.', 'flash_success');
        }        
    }
}
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
		'reset_password_token'=>array('type'=>'integer'),
        'notify_on_message'=>array('type'=>'integer'),
        'notify_on_answer'=>array('type'=>'integer'),
        //-- personal data
        'firstname'=>array('type'=>'string'),
        'lastname'=>array('type'=>'string'),
        'telephone'=>array('type'=>'string'),
        'country'=>array('type'=>'string'),
        'region'=>array('type'=>'string'),
        'city'=>array('type'=>'string'),
        'description'=>array('type'=>'string'),
        'show_email' => array('type'=>'integer'),
         //avatar
         'avatar'=>array('type'=>'hash')

	);
    
    var $validate = array(
        'mail'=>array(
            'notEmpty'=>array(
                'rule'=>'notEmpty',
                'required'=>true,
                'Campo obligatorio'
            ),
            'email' => array(
                'rule' => 'email',
                'message' => 'Email inválido'
            )
        ),
        'username'=>array(
            'notEmpty'=>array(
                'rule'=>'notEmpty',
                'required'=>true,
                'Campo obligatorio'
            ),
            'alphaNumeric'=>array(
                'rule'=>'alphaNumeric',
                'message'=>'Sólo letras y números están permitidos'
            ),
            'minLenght' => array(
                'rule' => array('minLength', 6),
                'message' => '6 caracteres mínimo'
            )
        ),
        'password'=>array(
            'minLenght' => array(
                'rule' => array('minLength', 8),
                'message' => '8 caracteres mínimo'
            )
        )
    );

	function mail_already_registered($mail) {
		$user = $this->find('first',array('conditions'=>compact('mail')));
		return !empty($user);
	}

	function user_already_registered($username) {
		$user = $this->find('first',array('conditions'=>compact('username')));
		return !empty($user);
	}

    function setAvatar($image,$uid){
        $user = $this->findById($uid);
        if(isset($user['User']['avatar']) && !empty($user['User']['avatar']) ){
           foreach(array('small','medium','large') as $size ){
            unlink($user['User']['avatar'][$size]['file_path']);
           }
        }
		$image = json_encode($image);
		return $this->execute(new MongoCode(
				"db.users.update({_id:ObjectId('$uid')},{\$set:{avatar:$image}},true,false)"
		));
    }

    function updateNotifications($uid,$notifications){
        $notifications = json_encode($notifications);
       	return $this->execute(new MongoCode(
				"db.users.update({_id:ObjectId('$uid')},{\$set:{notifications:$notifications}},true,false)"
		));
    }
}
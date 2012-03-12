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
        //TODO: borrar estos dos campos
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
        //account options
        'notify_on_message'=>array('type'=>'integer'),
        'notify_on_answer'=>array('type'=>'integer'),
        'get_newsletter'=>array('type'=>'integer'),
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
        'avatar'=>array('type'=>'hash'),
        'notifications'=>array('type'=>'hash')
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
        $count = count($notifications);
        if(count($notifications) > 30 ){
           //TODO guradar el numero maximo de notificaciones en la configuracion
           $notifications = array_slice($notifications, $count - 30) ;
        }
        $notifications = json_encode($notifications);
       	return $this->execute(new MongoCode(
				"db.users.update({_id:ObjectId('$uid')},{\$set:{notifications:$notifications}},true,false)"
		));
    }

    function notify($uid,$message,$links = array()){
       /* metodo para agregar notificaciones genericas
        * @param string $uid id del usuario
        * @param string $message mensaje que se va a mostrar en el panel de notificacion
        * @param array $links array asociativo que tiene los datos para crear un enlace que reemplazara a cada %s en el mensaje
        */
        $notification = array(
          'description' => $message,
          'has_been_read'=>0,
          'created' => time(),
          'links' =>  $links
        );
        $notification = json_encode($notification);
       	return $this->execute(new MongoCode(
				"db.users.update({_id:ObjectId('$uid')},{\$push:{notifications:$notification}},true,false)"
		));        
    }


    function notifyComment($user,$exchange){

     $links = array(
         $user['User']['username'] => array( 'controller'=>'users','action'=>'view',$user['User']['_id']),
     );

     switch($exchange['Exchange']['exchange_type_id']){
         case EXCHANGE_REQUEST:
             $exchange_label = "pedido";
         break;
         case EXCHANGE_OFFER:
             $exchange_label = "oferta";
         break;
         case EXCHANGE_EVENT:
             $exchange_label = "invitacion a evento";
         break;
         //TODO agregar servicio
         default:
             $exchange_label = "publicacion";
     }

     $links[$exchange_label] = array('controller'=>'exchanges','action'=>'view',$exchange['Exchange']['_id']);
     $this->notify($exchange['Exchange']['user_id'],"%s  ha comentado tu %s",$links);
    }

    function notifyMessage($user,$destiny,$cid){
        $links = array(
            $user['User']['username'] => array( 'controller'=>'users','action'=>'view',$user['User']['_id']),
            "mensaje privado" => array( 'controller'=>'conversations','action'=>'view',$cid)
        );
        $this->notify($destiny['User']['_id'],"%s te envió un %s",$links);

    }

}
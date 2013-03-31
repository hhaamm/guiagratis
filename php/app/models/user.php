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
    var $primaryKey = 'id';

    var $validate = array(
        'email'=>array(
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
                'rule'=>'alphaNumericDashUnderscore',
                'message'=>'Sólo letras, números y guión bajo están permitidos'
            ),
            'minLenght' => array(
                'rule' => array('minLength', 4),
                'message' => '4 caracteres mínimo'
            )
        ),
        'password'=>array(
            'minLenght' => array(
                'rule' => array('minLength', 8),
                'message' => '8 caracteres mínimo'
            )
        )
    );

    var $hasMany = array('Notification');

    function alphaNumericDashUnderscore($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
        $value = array_shift($check);
        return preg_match('|^[0-9a-zA-Z_-]*$|', $value);
    }

    function email_already_registered($email) {
        $user = $this->find('first',array('conditions'=>array('email' => $email)));
        return !empty($user);
    }

    function user_already_registered($username) {
        $user = $this->find('first',array('conditions'=>compact('username')));
        return !empty($user);
    }

    function setAvatar($uid, $originalImage){
        $user = $this->findById($uid);
        if(!empty($user['User']['avatar']) ){
            $pathinfo = pathinfo($user['User']['avatar']);
            // TODO: obtener configuración de imágenes del core.php
            // TODO: obtener un código genérico del core.php
            $sizes = array('square', 'medium_square', 'large_square');
            foreach($sizes as $size) {
                $filename = WWW_ROOT.'uploads/'.$pathinfo['filename'].'_'.$size.'.'.$pathinfo['extension'];
                if (file_exists($filename)) {
                    unlink($filename);
                }
            }
        }

        $this->id = $uid;
        return $this->saveField('avatar', $originalImage);
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
            $user['User']['username'] => array( 'controller'=>'users','action'=>'view',$user['User']['id']),
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

        $links[$exchange_label] = array('controller'=>'exchanges','action'=>'view',$exchange['Exchange']['id']);
        $this->notify($exchange['Exchange']['user_id'],"%s  ha comentado tu %s",$links);
    }

    function notifyMessage($user,$destiny,$cid){
        $links = array(
            $user['User']['username'] => array( 'controller'=>'users','action'=>'view',$user['User']['id']),
            "mensaje privado" => array( 'controller'=>'conversations','action'=>'view',$cid)
        );
        $this->notify($destiny['User']['id'],"%s te envió un %s",$links);

    }

    function getPassword($user_id) {
        $user = $this->find('first', array('conditions' => array('id' => $user_id)));
        return $user['User']['password'];
    }
}
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
?>
<h3>Configuración de la cuenta</h3>
<?php

echo $this->element('setting', array('id' => 'settings-notifications', 'title' => __('Notificaciones',true), 'desc' => __('Notifiaciones que recibís',true)));
echo $form->create('User');
echo $this->Form->input('notify_on_answer', array('label'=>__('Notificar cuando alguien responde una oferta o pedido',true), 'type'=>'checkbox'));
echo $this->Form->input('notify_on_message', array('label'=>__('Notificar cuando alguien te manda un mensaje',true), 'type'=>'checkbox'));
echo $this->Form->input('get_newsletter', array('label'=>__('Recibir newsletter semanal en mi mail',true), 'type'=>'checkbox'));
echo $form->submit(__('Guardar configuración',true));
echo $form->end();
echo $this->element('setting',array('footer' => true));


/*echo $this->element('setting', array('id' => 'settings-password', 'title' => __('Password',true), 'desc' => __('What you use to log in',true)));
echo $form->create('User', array('action' => 'change_password'));
echo $form->input('old_password', array('type' => 'password','label'=>__('Contraseña vieja',true)));
echo $form->input('new_password', array('type' => 'password','label'=>__('Contraseña nueva',true)));
echo $form->input('confirm_password', array('type' => 'password','label'=>__('Confirmar contraseña nueva',true)));
echo $form->submit(__('Cambiar contraseña',true));
echo $form->end();
echo $this->element('setting', array('footer' => true));*/

/*echo $this->element('setting', array('id' => 'settings-delete-account', 'title' => __('Borrar cuenta',true)));
 __('¿Estás seguro de que querés borrar tu cuenta?');
 echo "<br>";
__('Una vez que borres la cuenta, no podrás recuperarla.');
echo "<br>";
echo $form->create('User', array('action' => 'delete'));
echo $form->input('password', array('type' => 'password'));
echo $form->submit( __('Borrar mi cuenta',true));
echo $form->end();
echo $this->element('setting', array('footer' => true));*/
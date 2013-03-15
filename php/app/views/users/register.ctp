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
<fieldset>
	<legend>Registro de usuario</legend>
<?php

echo $this->Form->create('User');
echo $this->Form->input('username',array('label'=>'Nombre de usuario'));
echo $this->Form->input('email',array('label'=>'Email'));
echo $this->Form->input('password',array('label'=>'Contraseña'));
echo $this->Form->input('confirm_password', array('type'=>'password','label'=>'Confirmar contraseña'));
echo $this->Form->input('terms',array('type'=>'checkbox','label'=>"Acepto haber leído y entendido los <a href='/pages/tys' target='_blank'>términos y condiciones</a>"));
echo $this->Form->end('¡Registrame!');
?>
<p>* es posible que el mail de confirmación llegue a SPAM. Por favor después de registrarte, si no encontrás el mail revisá en tu carpeta de SPAM / CORREO NO DESEADO!</p>
</fieldset>
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
	<legend>Login</legend>
	<?php
	echo $form->create('User', array('action' => 'login', 'id' => 'login_user'));
	?>
	<div id="login-message"></div>
	<?php
	echo $form->input('mail',array('label'=>'Email'));
	echo $form->input('password',array('label'=>'Contraseña'));
	echo $form->end('Ingresar');
	?>
	<div class="clear"/>
	<div class="floatright"><a href="/users/forgot_password"><?__('Olvidé mi contraseña')?></a></div>
</fieldset>
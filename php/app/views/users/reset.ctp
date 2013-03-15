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
<? __('Ingresá el código que te mandamos a tu mail y la nueva contraseña.')?>

<?
echo $form->create('User');
echo $form->input('mail',array('label'=>'Email'));
echo $form->input('reset_password_code',array('label'=>'Código'));
echo $form->input('password',array('label'=>'Nueva contraseña'));
echo $form->input('confirm_password', array('type' => 'password','label'=>'Confirmar nueva contraseña'));
echo $form->end(__('Enviar',true));
?>
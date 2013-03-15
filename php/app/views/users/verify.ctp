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
<? if ($action == 'cellphone') { ?>
<? __('Verify your account! enter your cellphopne')?>
<?php
echo $form->create(null, array('class' => 'delivery-form'));
echo $form->input('cellphone', array('name' => 'cellphone'));
echo $form->end(__('Verify',true));
} else {
?>
<? echo sprintf(__("A token was just sent to %s . Please enter your code here and your account will be verified.", true), $cellphone); ?>
<?php
echo $form->create(null, array('class' => 'delivery-form', 'action' => 'verify_token'))    ;
echo $form->input('verify_token', array('name' => 'verify_token'));
echo $form->end(__('Verify',true));
}
?>
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
class UserHelper extends AppHelper {
    var $helpers = array('Html');

    function link($user) {
        return $this->Html->link($user['User']['username'], '/users/profile/'.$user['User']['id']);
    }

    // corta el nombre de usuario si este es demasiado largo
    // mostra sólo el nombre o si todo es demasiado largo
    function short_user_name($user , $max_length = 30) {
        if (strlen($user['User']['username']) <= $max_length) {
            return $user['User']['username'];
        }

        if (strlen($user['User']['firstname'].' '.$user['User']['lastname']) <= $max_length) {
            return $user['User']['firstname'].' '.$user['User']['lastname'];
        }

        return $user['User']['firstname'];
    }

    // es el nombre de usuario
    function short_user_link($user) {

    }
}
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

//constante que define hasta cuándo se considera nuevo un exchange.
//debe coincidir con el intervalo en el que corramos el script 
//con el cron.
define('NEWSLETTER_INTERVAL', '-1  week');

class NewsletterController extends AppController {
    var $uses = array('User', 'Exchange');
    var $components = array('Email');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('weekly');
    }
    
    // Runs weekly newsletter
    public function weekly() {
        if ($this->params["url"]["key"] != Configure::read("Newsletter.key")) {
            die("Invalid key param");
        }

        $this->Email->from = Configure::read('Mail.from');
        $this->Email->sendAs = 'html';
        $this->Email->template = 'newsletter';
        $this->Email->subject = 'Novedades en Guia Gratis';

        //nos fijamos si hay algo para mandar
        $exchanges = $this->Exchange->getLast(strtotime(NEWSLETTER_INTERVAL));
        
        if (empty($exchanges)) {
            die("No hay exchanges nuevos esta semana\n");
        }
        
        $this->set('exchanges', $exchanges);
        
        //obtenemos todos los usuarios que quieren obtener su newsletter.
        //TODO: ver por qué hay que poner comillitas en el get_newsletter!
        $users = $this->User->find('all', array(
            'conditions'=>array(
                'get_newsletter'=>'1',
                'active'=>1
            ),
            'limit'=>1000000
        ));
        
        if (empty($users)) {
            die("Ningún usuario tiene activado el newsletter.\n");
        }
        
        foreach ($users as $user) {
            $this->Email->to = $user['User']['mail'];
            $this->Email->send();
        }

        die("Done");
    }

    public function test() {
        if ($this->params["url"]["key"] != Configure::read("Newsletter.key")) {
            die("Invalid key param");
        }

        $this->Email->from = Configure::read('Mail.from');
        $this->Email->sendAs = 'html';
        $this->Email->template = 'newsletter';
        $this->Email->subject = 'Novedades en Guia Gratis';

        //nos fijamos si hay algo para mandar
        $exchanges = $this->Exchange->getLast(strtotime(NEWSLETTER_INTERVAL));
        
        if (empty($exchanges)) {
            die("No hay exchanges nuevos esta semana\n");
        }
        
        $this->set('exchanges', $exchanges);

        $this->Email->to = "ham1988@gmail.com";
        $this->Email->send();

        die("Done");
    }
}

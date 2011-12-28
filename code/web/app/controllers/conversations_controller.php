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

class ConversationsController extends AppController {

    var $uses = array('Conversation', 'User');

    function add($to = null) {
        if ($this->data) {
            $this->data['Conversation']['from'] = $this->Auth->user('_id');
            $this->data['Conversation']['from_new_message'] = 0;
            $this->data['Conversation']['to_new_message'] = 1;
            $this->data['Conversation']['messages'][0]['created'] = time();
            $this->data['Conversation']['messages'][0]['from'] = $this->Auth->user('_id');

            if ($this->Conversation->save($this->data)) {
                $destiny = $this->User->findById($this->data['Conversation']['to']);

                if ($destiny['User']['notify_on_message']) {
                    $this->set(array(
                        'cid' => $this->Conversation->id,
                        'text' => $this->data['Conversation']['messages'][0]['text'],
                        'username' => $this->Auth->user('username')
                    ));
                    $this->sendMail($destiny['User']['mail'], 'Tenés un nuevo mensaje', 'message_notification');
                }

                $this->Session->setFlash('Mensaje enviado');
                $this->redirect('/conversations/view/' . $this->Conversation->id);
            } else {
                $this->Session->setFlash('No se pudo enviar el mensaje','flash_failure');
            }
        } else {
            $this->data['Conversation']['to'] = $to;
        }
    }

    function index() {
        $conversations = $this->Conversation->byUser($this->uid);

        $this->set(compact('conversations'));
    }

    function view($cid) {
        $conversation = $this->Conversation->view($cid, $this->uid);

        if (!$conversation) {
            $this->getBack('Conversación no encontrada','flash_failure');
        }
        $this->set(compact('conversation'));
    }

    function answer() {
        $answer = array(
            'from' => $this->Auth->user('_id'),
            'created' => time(),
            'text' => $this->data['Conversation']['text']
        );
        $result = $this->Conversation->answer($this->data['Conversation']['_id'], $answer, $this->uid);

        $conversation = $this->Conversation->findById($this->data['Conversation']['_id']);
        if ($conversation['Conversation']['to'] == $this->Auth->user('_id')) {
            $destiny = $this->User->findById($conversation['Conversation']['from']);
        } else {
            $destiny = $this->User->findById($conversation['Conversation']['to']);
        }

        if ($destiny['User']['notify_on_message']) {
            $this->set(array(
                'cid' => $conversation['Conversation']['_id'],
                'text' => $this->data['Conversation']['text'],
                'username' => $this->Auth->user('username')
            ));
            $this->sendMail($destiny['User']['mail'], 'Tenés un nuevo mensaje', 'message_notification');
        }
        $this->getBack('Mensaje enviado','flash_success');
    }

}
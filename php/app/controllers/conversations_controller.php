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

    var $uses = array('Message', 'User');

    function add($to = null) {
        if ($this->data) {
            $this->data['Message']['sender_id'] = $this->Auth->user('id');
            $this->data['Message']['start_conversation'] = 1;
            $this->data['Message']['from_new_message'] = 0;
            $this->data['Message']['to_new_message'] = 1;
            $this->data['Message']['messages'][0]['created'] = time();
            $this->data['Message']['messages'][0]['from'] = $this->Auth->user('id');

            if ($this->Message->save($this->data)) {
                // TODO: arreglar las notificationes
                /*
                $destiny = $this->User->findById($this->data['Conversation']['to']);

                if ($destiny['User']['notify_on_message']) {
                    $this->set(array(
                        'cid' => $this->Conversation->id,
                        'text' => $this->data['Conversation']['messages'][0]['text'],
                        'username' => $this->Auth->user('username')
                    ));
                    $this->sendMail($destiny['User']['mail'], 'Tenés un nuevo mensaje', 'message_notification');
                    }
                    $this->User->notifyMessage($this->Auth->user(), $destiny, $this->Conversation->id);
                */
                $this->Session->setFlash('Mensaje enviado', 'flash_success');
                $this->redirect('/conversations/view/' . $this->Message->id);
            } else {
                foreach($this->Message->validationErrors as $field => $error) {
                    $this->Session->setFlash($field.': '.$error, 'flash_failure');
                }
            }
        } else {
            $this->data['Message']['receiver_id'] = $to;
        }
    }

    function index() {
        $this->Message->contain(array('Sender', 'Receiver'));
        $conversations = $this->Message->find('all', array(
            'conditions' => array(
                'thread_id' => 'IS NULL',
                'or' => array(
                    array('sender_id' => $this->uid),
                    array('receiver_id' => $this->uid)
                )
            )
        ));

        $this->set(compact('conversations'));
    }

    function view($messageId) {
        $this->Message->contain(array('Sender', 'Receiver'));
        $conversation = $this->Message->find('all', array(
            'conditions' => array(
                'or' => array(
                    array('Message.id' => $messageId),
                    array('thread_id' => $messageId)
                )
            ),
            'order' => 'Message.id ASC'
        )
        );

        if (!$conversation) {
            $this->getBack('Conversación no encontrada','flash_failure');
        }

        $this->data['Message']['receiver_id'] = $conversation[0]['Receiver']['id'];
        $this->data['Message']['thread_id'] = $conversation[0]['Message']['id'];

        // TODO: get conversation (other messages)
        $this->set(compact('conversation'));
    }

    function answer() {
        if ($this->data) {
            $this->data['Message']['sender_id'] = $this->Auth->user('id');
            $this->data['Message']['sender_id'] = time();
            $this->data['Message']['sender_id'] = $this->Auth->user('id');

            if ($this->Message->save($this->data['Message'])) {
                $this->getBack('Mensaje enviado','flash_success');
            } else {
                // TODO: make this a generic method in app_controller
                foreach($this->Message->validationErrors as $field => $error) {
                    $this->Session->setFlash($field.': '.$error, 'flash_failure');
                }
            }
        }

        /*
        $conversation = $this->Conversation->findById($this->data['Conversation']['id']);
        if ($conversation['Conversation']['to'] == $this->Auth->user('id')) {
            $destiny = $this->User->findById($conversation['Conversation']['from']);
        } else {
            $destiny = $this->User->findById($conversation['Conversation']['to']);
        }

        if ($destiny['User']['notify_on_message']) {
            $this->set(array(
                'cid' => $conversation['Conversation']['id'],
                'text' => $this->data['Conversation']['text'],
                'username' => $this->Auth->user('username')
            ));
            $this->sendMail($destiny['User']['mail'], 'Tenés un nuevo mensaje', 'message_notification');
        }
        $this->User->notifyMessage($this->Auth->user(),$destiny,$this->data['Conversation']['id']);        
        */
    }

}
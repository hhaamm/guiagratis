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
class ExchangesController extends AppController {
    var $uses = array('Exchange','User');
	var $components = array('Geo','Email','Upload');
	var $helpers = array('Exchange');

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index','get','view','search');
	}

	function index() {
        //This is where we will be first.
        $this->set_start_point();
        $this->set('start_address', Configure::read('GoogleMaps.DefaultAddress'));
	}

    function search(){
       $options = array(
			'limit'=>35,
			'page'=>1,
            'conditions' => array('state'=>EXCHANGE_PUBLISHED)
       );
       $mode = 0;
       if(isSet($this->params['url']['query'])){
        if($this->params['url']['query']==""){
            $this->Session->setFlash('Ingrese una consulta en el cuadro de texto');
            $this->set('mode',0);
            return;
        }
        if(!isSet($this->params['url']['mode']) || $this->params['url']['mode']=="0"){
          $options['conditions']['tags'] = array('$in' => explode(',', $this->params['url']['query']));
        }else{
          $query = explode(' ',$this->params['url']['query'] );
          $query = implode('.*.',$query);
          $options['conditions']['title'] = array('$regex' => new MongoRegex('/'.$query.'/i'));
          $mode = 1;
        }
        if(isSet($this->params['url']['type']) && !empty($this->params['url']['type'])){
         $options['conditions']['exchange_type_id']=(int)$this->params['url']['type'];
        }
        $exchanges = $this->Exchange->find('all',$options);
        $this->set(compact('exchanges'));
       }
       $this->set(compact('mode'));                   
    }

	function add_request() {
		if ($this->data) {
			$this->data['Exchange']['exchange_type_id'] = Configure::read('ExchangeType.Request');
			$this->data['Exchange']['exchange_state_id'] = Configure::read('ExchangeState.Published');
			$this->data['Exchange']['lng']=(float)$this->data['Exchange']['lng'];
			$this->data['Exchange']['lat']=(float)$this->data['Exchange']['lat'];
			$this->data['Exchange']['user_id']=$this->Auth->user('_id');
			$this->data['Exchange']['created']=time();
			$this->data['Exchange']['modified']=time();
			$this->data['Exchange']['state'] = EXCHANGE_PUBLISHED;
			$this->data['Exchange']['photos'] = array();
            $this->data['Exchange']['username'] = $this->Auth->user('username');
            if ($this->Exchange->save($this->data)) {
                $this->Session->setFlash('¡El pedido fue publicado!');
                $this->redirect(array('controller'=>'exchanges','action'=>'edit_photos',$this->Exchange->id));
            }
		}

		$this->set_start_point();
	}

	function add_offer() {
		if ($this->data) {
			$this->data['Exchange']['exchange_type_id'] = Configure::read('ExchangeType.Offer');
			$this->data['Exchange']['exchange_state_id'] = Configure::read('ExchangeState.Published');
			$this->data['Exchange']['lng']=(float)$this->data['Exchange']['lng'];
			$this->data['Exchange']['lat']=(float)$this->data['Exchange']['lat'];
			$this->data['Exchange']['user_id']=$this->Auth->user('_id');
			$this->data['Exchange']['created']=time();
			$this->data['Exchange']['modified']=time();
			$this->data['Exchange']['state'] = EXCHANGE_PUBLISHED;
			$this->data['Exchange']['photos'] = array();
            $this->data['Exchange']['username'] = $this->Auth->user('username');
			if ($this->Exchange->save($this->data)) {
                $this->Session->setFlash('¡La oferta fue publicada!');
                $this->redirect(array('controller'=>'exchanges','action'=>'edit_photos',$this->Exchange->id));
            }
		}

		$this->set_start_point();
	}

	/*
	 * Ajax function that returns exchanges with some parameters
	 */
	function get() {
		$fields_validation = $this->require_fields($_REQUEST,array('north','west','south','east','exchange_type_id', 'query'));
		if ($fields_validation !== true) {
			$this->result(false, $fields_validation);
		}

		//TODO: poner un órden copado, por "popularidad" o algo así.
		$options = array(
			'limit'=>35,
			'page'=>1,
			'conditions'=>array(
				'lat'=>array('$lt'=>(float)$_REQUEST['north'],'$gt'=>(float)$_REQUEST['south']),
				'lng'=>array('$gt'=>(float)$_REQUEST['west'],'$lt'=>(float)$_REQUEST['east']),
				'state'=>EXCHANGE_PUBLISHED
			)
		);
		if ($_REQUEST['exchange_type_id'] != Configure::read('ExchangeType.All')) {
			$options['conditions']['exchange_type_id']=(int)$_REQUEST['exchange_type_id'];
		}
        if (!empty($_REQUEST['query'])) {
            $options['conditions']['tags'] = array('$in' => explode(',', $_REQUEST['query']));
        }
        
		$exchanges = $this->Exchange->find('all',$options);
		$this->result(true,'',compact('exchanges'));
	}

	private function set_start_point() {
		if (isset($_COOKIE[Configure::read('CookieName.LastPointSearched')])) {
            $start_point = $_COOKIE[Configure::read('CookieName.LastPointSearched')];
        } else {
            try {
                //Get country and city by IP geolocalization.
                $start_point = $this->Geo->localizeFromIpTwo();
            } catch (Exception $e) {
                $start_point = Configure::read('GoogleMaps.DefaultPoint');
            }
        }
		$this->set('start_point', $start_point);
	}

	function view($id) {
		$exchange = $this->Exchange->read(null, $id);
		if (empty($exchange)) {
			debug("Exchange is null");
		}
        foreach($exchange['Exchange']['comments'] as $i => $comment){
            $comment_owner = $this->User->findById($comment['user_id']);
            $exchange['Exchange']['comments'][$i]['user'] = $comment_owner['User'];
        }
        $owner = $this->User->findById($exchange['Exchange']['user_id']);
        $title_for_layout = $exchange['Exchange']['title'];
        $this->set(compact('owner','user','exchange','title_for_layout'));
	}

	function edit($eid) {
        $exchange = $this->Exchange->read(null, $eid);
        $owner = $this->User->findById($exchange['Exchange']['user_id']);
        //TODO: ver si esto se puede cambiar por los datos del usuario en sesión
        //(menos llamadas a la base)
        $user =  $this->User->findById($this->Auth->user('_id'));
        if($owner['User']['_id'] !=  $user['User']['_id'] ){
            $this->Session->setFlash('No tiene permisos para realizar esta acción',true);
            $this->redirect(array('action' => 'view',$eid));
            return;
        }
		if (!$this->data) {
			$this->data = $exchange;
		} else {
			$this->data['Exchange']['lng'] = (float)$this->data['Exchange']['lng'];
			$this->data['Exchange']['lat'] = (float)$this->data['Exchange']['lat'];
			$result = $this->Exchange->save($this->data);
			if ($result) {
				$this->Session->setFlash('Cambios guardados');
			} else {
				$this->Session->setFlash('Un error ha ocurrido',true);
			}
		}
		$this->set('start_point',array('latitude'=>$this->data['Exchange']['lat'],'longitude'=>$this->data['Exchange']['lng']));
	}

	function add_comment() {
		$eid = $this->data['Exchange']['_id'];
		$comment = array(
			'text'=>$this->data['Exchange']['comment'],
			'user_id'=>$this->Auth->user('_id'),
			'username'=>$this->Auth->user('username'),
			'created'=>time()
		);
		if ($this->Exchange->addComment($eid,$comment)) {
            $exchange = $this->Exchange->findById($eid);
            $creator = $this->User->findById($exchange['Exchange']['user_id']);
            //checkeamos que no sea el mismo usuario el que se auto-responde y que quiera notificaciones.
            if ($creator['User']['_id'] != $this->Auth->user('_id') && $creator['User']['notify_on_answer']) {
                $this->set($comment);
                $this->set(compact('eid'));
                $this->sendMail($creator['User']['mail'], 'Alguien comentó tu artículo en Guia Gratis', 'comment_notification');
            }
            $this->getBack("Tu comentario ha sido añadido");
        } else {
            $this->getBack("Hubo un error al agregar el comentario");
        }
		
	}

	/*
	 * Lists all exchanges related with the current user
	 */
	function own() {
		$exchanges = $this->Exchange->find('all',array(
			'conditions'=>array('user_id'=>$this->Auth->user('_id')),
			'limit'=>35
		));

		if(!$exchanges) {
			$exchanges = array();
		}

		$this->set(compact('exchanges'));
	}

	function edit_photos($exchange_id = null) {
        if (!$exchange_id) {
			$this->getBack("URL inválida");
		}
        $e = $this->Exchange->read(null, $exchange_id);

        $owner = $this->User->findById($e['Exchange']['user_id']);
        $user =  $this->User->findById($this->Auth->user('_id'));
        if($owner['User']['_id'] !=  $user['User']['_id'] ){
            $this->Session->setFlash('No tiene permisos para realizar esta acción',true);
            $this->redirect(array('action' => 'view',$exchange_id));
            return;
        }

		$this->set(compact('exchange_id','e'));
	}

	function add_photo() {
		$this->autoLayout = false;
		$result = $this->Upload->images(array('images' => array(
			'square' => array('width' => 50, 'height' => 50, 'keep_aspect_ratio' => true),
			'small' => array('width' => 500, 'keep_aspect_ratio' => true)
		),
			'dest_path' => WWW_ROOT.'uploads',
			'file_field'=>'photo'));
		$img_id = uniqid(null, true);
		$this->Exchange->addPhoto($this->data['Photo']['eid'],array('id'=>$img_id,'square'=>$result['square'],'small'=>$result['small']),$this->uid);
		$img_url = $result['square']['url'];
		$this->set(compact('img_url', 'img_id'));
	}

	function set_default_photo($eid,$pid) {
		$this->autoLayout = false;
		$result = $this->Exchange->setDefaultPhoto($eid,$pid,$this->uid);
		$this->redirect('/exchanges/edit_photos/'.$eid);
	}

	function delete_photo($eid, $pid) {
		$this->autoRender = false;
		
		$result = $this->Exchange->deletePhoto($eid, $pid, $this->uid);
		$this->redirect('/exchanges/edit_photos/'.$eid);
	}

	function finalize($eid) {
		$result = $this->Exchange->finalize($eid, $this->uid);
		$this->Session->setFlash('El intercambio ha finalizado');
		$this->redirect('/exchanges/own');
	}
    
    // admin sections
    function admin_index() {
        $exchanges = $this->Exchange->find('all', array(
            'limit'=>500
        ));
        $count = $this->Exchange->find('count');
        $countOffer = $this->Exchange->find('count', array(
            'conditions'=>array(
                'exchange_type_id'=>Configure::read('ExchangeType.Offer')
            )
        ));
        $countRequest = $this->Exchange->find('count', array(
            'conditions'=>array(
                'exchange_type_id'=>Configure::read('ExchangeType.Request')
            )
        ));
        $usersActive = $this->User->find('count', array(
            'conditions'=>array(
                'active'=>1
            )
        ));
        $exchangesByUser = round($count / $usersActive, 2);
        $offersByUser = round($countOffer / $usersActive, 2);
        $requestsByUser = round($countRequest / $usersActive, 2);
        $this->set(compact('exchanges', 'count', 'countOffer', 'countRequest', 'exchangesByUser', 'offersByUser', 'requestsByUser'));
    }
}

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
	var $components = array('Geo','Email','Upload');
	var $helpers = array('Exchange');

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index','get','view');
	}

	function index() {
        //This is where we will be first.
        $this->set_start_point();
        $this->set('start_address', Configure::read('GoogleMaps.DefaultAddress'));
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
			$this->Exchange->save($this->data);
			$this->Session->setFlash('¡El pedido fue publicado!');
			$this->redirect(array('controller'=>'exchanges','action'=>'edit_photos',$this->Exchange->id));
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
			$this->Exchange->save($this->data);
			$this->Session->setFlash('¡La oferta fue publicada!');
			$this->redirect(array('controller'=>'exchanges','action'=>'edit_photos',$this->Exchange->id));
		}

		$this->set_start_point();
	}

	/*
	 * Ajax function that returns exchanges with some parameters
	 */
	function get() {
		$fields_validation = $this->require_fields($_REQUEST,array('north','west','south','east','exchange_type_id'));
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
		$this->set(compact('exchange'));
	}

	function edit($eid) {
		if (!$this->data) {
			$exchange = $this->Exchange->read(null, $eid);
			$this->data = $exchange;
		} else {
			$this->data['Exchange']['lng'] = (float)$this->data['Exchange']['lng'];
			$this->data['Exchange']['lat'] = (float)$this->data['Exchange']['lat'];
			$result = $this->Exchange->save($this->data);
			if ($result) {
				$this->Session->setFlash('Cambios guardados');
			} else {
				$this->Session->setFlash('Un error ha ocurrido');
			}
		}
		$this->set('start_point',array('latitude'=>$this->data['Exchange']['lat'],'longitude'=>$this->data['Exchange']['lng']));
	}

	function add_comment() {
		$eid = $this->data['Exchange']['_id'];
		$comment = array(
			'text'=>$this->data['Exchange']['comment'],
			'user_id'=>$this->Auth->user('id'),
			'username'=>$this->Auth->user('username'),
			'created'=>time()
		);
		$this->Exchange->addComment($eid,$comment);
		$this->getBack("Tu comentario ha sido añadido");
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
		$e = $this->Exchange->findById($exchange_id);
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
		debug($result);
		$this->Session->setFlash('El intercambio ha finalizado');
		$this->redirect('/exchanges/own');
	}

	function mailtest($mail=null) {
		echo "Bla";
		$this->autoRender = false;
		if ($mail == null) {
			$mail = 'ham1988@gmail.com';
		} 
		mail($mail,'Guia gratis', 'This is a test message', 'From:mensaje@guiagratis.com.ar');
	}
    
    // admin sections
    function admin_index() {
        
    }
}

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
    var $uses = array('Exchange', 'User', 'ExchangeComment');
    var $components = array('Geo', 'Email', 'Upload','RequestHandler', 'Image');
    var $helpers = array('Exchange', 'User', 'Html', 'Text');
    var $paginate = array(
        'Exchange'=>array(
            'order'=> 'created DESC',
            'limit'=>26,
            'contain' => array('User', 'Photo')
        )
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('index', 'get', 'view', 'search', 'view_photo','rate', 'lista', 'map', 'listar_ajax', 'api_create');
    }

    function index() {
        // redirige a la acción por default
        $this->redirect(array('action'=>'lista'));
    }

    function map() {
        if( $this->RequestHandler->isRss() ){
            $exchanges = $this->Exchange->find('all', array(
                'order'=>array('created'=>-1),
                'limit'=>40,
                'conditions'=>array(
                    'state'=>EXCHANGE_PUBLISHED
                )
            ));
            $this->set(compact('exchanges'));
        } else {
            //This is where we will be first.
            $this->set_start_point();
            $this->set('start_address', Configure::read('GoogleMaps.DefaultAddress'));
        }
    }

    function search() {
        $options = array(
            'limit' => 35,
            'page' => 1,
            'conditions' => array('state' => EXCHANGE_PUBLISHED)
        );
        $mode = 0;
        if (isset($this->params['url']['query'])) {
            if ($this->params['url']['query'] == "") {
                $this->Session->setFlash('Ingrese una consulta en el cuadro de texto', 'flash_warning');
                $this->set('mode', 0);
                return;
            }
            if (!isSet($this->params['url']['mode']) || $this->params['url']['mode'] == "0") {
                $options['conditions']['tags'] = array('$in' => explode(',', $this->params['url']['query']));
            } else {
                $query = explode(' ', $this->params['url']['query']);
                $query = implode('.*.', $query);
                $options['conditions']['title'] = array('$regex' => new MongoRegex('/' . $query . '/i'));
                $mode = 1;
            }
            if (isSet($this->params['url']['type']) && !empty($this->params['url']['type'])) {
                $options['conditions']['exchange_type_id'] = (int) $this->params['url']['type'];
            }
            $this->Exchange->catchFinalizedEvents = true;
            $exchanges = $this->Exchange->find('all', $options);
            $this->set(compact('exchanges'));
        }else{
            if(isset($this->params['url']['mode'])){
                $mode =  $this->params['url']['mode'] == "0" ? 0 : 1;
            }
        }
        $this->set(compact('mode'));
    }

    function lista() {
        $options = array(
            'limit' => 40,
            'order' => 'created DESC',
            'page' => 1,
            'conditions' => array(
                'state' => EXCHANGE_PUBLISHED
            )
        );

        $this->Exchange->catchFinalizedEvents = true;

        if ($this->data) {
            // nos pasaron filtros, filtramos
            $conditions = array('state'=>EXCHANGE_PUBLISHED);
            $types = array();
            if ($this->data['Filter']['exchange_type']) {
                foreach($this->data['Filter']['exchange_type'] as $type) {
                    $types[] = (int)$type;
                }
                $conditions['exchange_type_id'] = $types;
            }
            $query = $this->data['Filter']['query'];
            
            if (!empty($query)) {
                $conditions['or'] = array(
                    array('Exchange.tags LIKE' =>'%'.$query.'%'),
                    array('Exchange.title LIKE'=> '%'.$query.'%'),
                    array('Exchange.detail LIKE' => '%'.$query.'%'),
                    array('Exchange.country LIKE' =>'%'.$query.'%'),
                    array('Exchange.province LIKE' =>'%'.$query.'%'),
                    array('Exchange.locality LIKE' =>'%'.$query.'%'),
                );
            }

            $this->Session->write('SearchFilter', array('types'=>$types, 'conditions'=>$conditions, 'query'=>$query));
        } else {

            if ($this->Session->check('SearchFilter')) {
                $conditions = $this->Session->read('SearchFilter.conditions');
                $types = $this->Session->read('SearchFilter.types');
                $query = $this->Session->read('SearchFilter.query');                
            } else {
                // valores por default para los filtros
                $types = array(1,2,3,4);
                // no tenemos parámetros que filtrar
                $conditions = array('state'=>EXCHANGE_PUBLISHED);
                $query = '';
            }

            $this->data = array(
                'Filter'=>array(
                    'exchange_type'=>$types,
                    'query'=>$query
                )
            );
        }

        $exchanges = $this->paginate('Exchange', $conditions);

        $this->set(compact('exchanges'));
    }

    // devuelve los datos de las coordenadas que se pasan como parámetro
    function reverse_geocoding() {
        $this->autoRender = false;
        $lat = $this->params['url']['lat'];
        $lng = $this->params['url']['lng'];
        $file = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&sensor=false");

        $obj = json_decode($file);
        $level = -1;
        foreach($obj->results as $result) {
            // tratamos de obtener el resultado mas preciso
            if (in_array('administrative_area_level_2', $result->types) && $level < 2) {
                $address = explode(',', $result->formatted_address);
                $locality = $address[0];
                $province = $address[1];
                $country = $address[2];
                $level = 2;
            }
            if (in_array('administrative_area_level_1', $result->types) && $level < 1) {
                $address = explode(',', $result->formatted_address);
                $province = $address[0];
                $country = $address[1];
                $level = 1;
            }
            if (in_array('administrative_area_level_1', $result->types) && $level < 1) {
                $address = explode(',', $result->formatted_address);
                $country = $address[0];
                $level = 0;
            }
        }

        echo json_encode(compact('country', 'province', 'locality'));
    }

    function add_request() {
        if ($this->data) {
            $this->add_exchange(EXCHANGE_REQUEST, '¡El pedido fue publicado!');
        }

        $this->set_start_point();
    }

    function add_offer() {
        if ($this->data) {
            $this->add_exchange(EXCHANGE_OFFER, '¡La oferta fue publicada!');
        }

        $this->set_start_point();
    }

    function add_event() {
        if ($this->data) {
            $this->add_exchange(EXCHANGE_EVENT, '¡El evento fue publicado!');
        }

        $this->set_start_point();
    }

    function add_service() {
        if ($this->data) {
            $this->add_exchange(EXCHANGE_SERVICE, '¡El servicio fue publicado!');
        }

        $this->set_start_point();
    }

    private function add_exchange($exchange_type_id, $message, $ajax = false) {
        $this->data['Exchange']['exchange_type_id'] = $exchange_type_id;
        $this->data['Exchange']['exchange_state_id'] = Configure::read('ExchangeState.Published');
        $this->data['Exchange']['lng'] = (float) $this->data['Exchange']['lng'];
        $this->data['Exchange']['lat'] = (float) $this->data['Exchange']['lat'];
        if (!$ajax) {  // TODO: advertencia de seguridad: acordarse de remover esto antes de salir a PROD
            $this->data['Exchange']['user_id'] = $this->Auth->user('id');
        }
        $this->data['Exchange']['state'] = EXCHANGE_PUBLISHED;
        $this->data['Exchange']['photos'] = array();
        if (!$ajax) {    // TODO: remover esto antes de salir a PROD
            $this->data['Exchange']['username'] = $this->Auth->user('username');
        }

    if ($ajax) {
      return $this->Exchange->save($this->data);
    }

    if ($this->Exchange->save($this->data)) {
            $this->Session->setFlash($message, 'flash_success');
            $this->Session->write('Facebook.share_exchange', true);
            $this->redirect(array('controller' => 'exchanges', 'action' => 'edit_photos', $this->Exchange->id));
        } else {
            foreach($this->Exchange->validationErrors as $field => $message) {
                $this->Session->setFlash($field.": ".$message, 'flash_failure');
            }
        }
    }

    /*
     * Ajax function that returns exchanges with some parameters
     */

    function get() {
        $fields_validation = $this->require_fields($_REQUEST, array('north', 'west', 'south', 'east', 'exchange_type_id', 'query'));
        if ($fields_validation !== true) {
            $this->result(false, $fields_validation);
        }

        //TODO: poner un órden copado, por "popularidad" o algo así.
        $options = array(
            'limit' => 40,
            'order' => array('created' => -1),
            'page' => 1,
            'conditions' => array(
                'lat' => array('$lt' => (float) $_REQUEST['north'], '$gt' => (float) $_REQUEST['south']),
                'lng' => array('$gt' => (float) $_REQUEST['west'], '$lt' => (float) $_REQUEST['east']),
                'state' => EXCHANGE_PUBLISHED
            )
        );
        if ($_REQUEST['exchange_type_id'] != Configure::read('ExchangeType.All')) {
            $options['conditions']['exchange_type_id'] = (int) $_REQUEST['exchange_type_id'];
        }
        if (!empty($_REQUEST['query'])) {
            $options['conditions']['tags'] = array('$in' => explode(',', $_REQUEST['query']));
        }

        $this->Exchange->catchFinalizedEvents = true;
        $exchanges = $this->Exchange->find('all', $options);
        $this->result(true, '', compact('exchanges'));
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
        $this->Exchange->contain(array('Comment' => array('User'), 'Photo'));
        $exchange = $this->Exchange->read(null, $id);        
        $owner = $this->User->findById($exchange['Exchange']['user_id']);

        $title_for_layout = $exchange['Exchange']['title'];

        $rates = $this->Exchange->getTotalRates($exchange);

        $share_exchange_on_facebook = $this->Session->read('Facebook.share_exchange');
        $this->Session->delete('Facebook.share_exchange');

        $this->set(compact('owner', 'exchange','rates','title_for_layout', 'share_exchange_on_facebook'));
    }

    function edit($eid) {
        $exchange = $this->Exchange->read(null, $eid);

        if ($this->_cantEditExchange($exchange)) {
            $this->Session->setFlash('No tiene permisos para realizar esta acción', 'flash_failure');
            $this->redirect(array('action' => 'view', $eid));
            return;
        }
        $this->set('creator', $this->User->findById($exchange['Exchange']['user_id']));
        if (!$this->data) {
            $this->data = $exchange;
        } else {
            $this->data['Exchange']['lng'] = (float) $this->data['Exchange']['lng'];
            $this->data['Exchange']['lat'] = (float) $this->data['Exchange']['lat'];
            $result = $this->Exchange->save($this->data);
            if ($result) {
                $this->Session->setFlash('Cambios guardados','flash_success');
                $this->redirect('/exchanges/view/' . $eid);
            } else {
                $this->data = $this->Exchange->read(null, $eid);
                $this->Session->setFlash('Un error ha ocurrido','flash_failure');
            }
        }
        if(isset($this->data['Exchange']['start_date'])){
            //formateamos en el formato que usa CakePHP
            $this->data['Exchange']['start_date'] = array(
                'hour' => date('H', $this->data['Exchange']['start_date']->sec),
                'min' => date('i', $this->data['Exchange']['start_date']->sec),
                'sec' => date('s', $this->data['Exchange']['start_date']->sec),
                'year' => date('Y', $this->data['Exchange']['start_date']->sec),
                'month' => date('m', $this->data['Exchange']['start_date']->sec),
                'day' => date('d', $this->data['Exchange']['start_date']->sec)
            );
        }
        $this->set('start_point', array('latitude' => $this->data['Exchange']['lat'], 'longitude' => $this->data['Exchange']['lng']));
    }

    function add_comment() {
        $this->data['ExchangeComment']['user_id'] = $this->Auth->user('id');        
        
        if ($this->ExchangeComment->save($this->data)) {
            /*
            $exchange = $this->Exchange->findById($this->data['ExchangeComment']['exchange_id']);
            $creator = $this->User->findById($exchange['Exchange']['user_id']);
            //checkeamos que no sea el mismo usuario el que se auto-responde y que quiera notificaciones.
            if ($creator['User']['id'] != $this->Auth->user('id')) {
                if($creator['User']['notify_on_answer']){
                    $this->set($comment);
                    $this->set(compact('eid'));
                    $this->sendMail($creator['User']['mail'], 'Alguien comentó tu artículo en Guia Gratis', 'comment_notification');
                }
                
            }
            $this->User->notifyComment($this->Auth->user(),$exchange);
            */
            $this->getBack("Tu comentario ha sido añadido", 'flash_success');
        } else {
            foreach ($this->ExchangeComment->validationErrors as $field => $error) {
                $this->Session->setFlash($field.': '.$error, 'flash_failure');
            }
            $this->getBack();
        }       
    }

    function remove_comment($eid, $i) {
        if (!$this->Auth->user('admin')) {
            //por ahora solo los admins pueden elimiar comentarios
            //despues se podrian agregar otros rangos.
            $this->getBack('No tiene permisos para realizar esta acción', 'flash_failure');
            return;
        }

        $this->Exchange->removeComment($eid, $i);
        $this->getBack("Comentario eliminado", 'flash_success');
    }

    /*
     * Lists all exchanges related with the current user
     */

    function own() {
        $exchanges = $this->Exchange->find('all', array(
            'conditions' => array('user_id' => $this->Auth->user('id')),
            'limit' => 35
        ));
        if (!$exchanges) {
            $exchanges = array();
        }

        $this->set(compact('exchanges'));
    }

    function edit_photos($exchange_id = null) {
        if (!$exchange_id) {
            $this->getBack("URL inválida");
        }
        $this->Exchange->contain('Photo');
        $e = $this->Exchange->read(null, $exchange_id);

        if ($this->_cantEditExchange($e)) {
            $this->Session->setFlash('No tiene permisos para realizar esta acción', 'flash_failure');
            $this->redirect(array('action' => 'view', $exchange_id));
            return;
        }

        $this->set(compact('exchange_id', 'e'));
    }

    function add_photo() {
        $this->autoLayout = false;        

        $this->Exchange->contain('Photo');
        $e = $this->Exchange->findById($this->data['Photo']['exchange_id']);

		if ($e['Exchange']['user_id'] != $this->uid) {
			$this->log("User {$this->uid} trying to add photos to exchange with id = ${$eid}. Denied");
			return false;
		}

        if (!$_FILES) {
            die("No files uploaded");
        }

        //move_uploaded_file
        $img_id = uniqid(null, true);
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        // TODO: check they are not uploading .exe, .sh, etc. files
        $file_name = $img_id.'.'.$ext;
        $filepath = WWW_ROOT.'uploads/'.$file_name;
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $filepath)) {
            die("Hubo un error al subir la imágen");
        }

        // TODO: put all this config in core.php
        $square_filepath = WWW_ROOT.'uploads/'.$img_id.'_square.'.$ext;
        $small_filepath = WWW_ROOT.'uploads/'.$img_id.'_small.'.$ext;
        copy($filepath, $square_filepath);
        copy($filepath, $small_filepath);
        $this->Image->resizeImg($square_filepath, 50); // TODO: keep aspect ratio
        $this->Image->resizeImg($small_filepath, 500); // TODO: keep aspect ratio

        // TODO: make a helper for photos

		if (empty($e['Photo'])) {
			$this->data['Photo']['is_default'] = 1;
		}
        $this->data['Photo']['file_name'] = $file_name;

        if ($this->Exchange->Photo->save($this->data)) {
            $img_url = '/uploads/'.$file_name;
            $img_id = $file_name;
            $this->set(compact('img_url', 'img_id'));
        } else {
            // move to view?
            foreach($this->Exchange->Photo->validationErrors as $field => $error) {
                echo $field.': '.$error.'<br/>';
            }
            die();
        }
    }

    function set_default_photo($eid, $pid) {
        $this->autoLayout = false;
        $result = $this->Exchange->setDefaultPhoto($eid, $pid, $this->uid);
        $this->redirect('/exchanges/edit_photos/' . $eid);
    }

    function delete_photo($eid, $pid) {
        $this->autoRender = false;

        // TODO: delete original, small and square, if they exists
        $this->Exchange->Photo->delete($pid);

        // TODO: handle default photo (set another default photo)
        $this->redirect('/exchanges/edit_photos/' . $eid);
    }

    function finalize($eid) {
        $exchange = $this->Exchange->find('first', array('conditions' => array('id' => $eid)));
        if ($this->_cantEditExchange($exchange)) {
            $this->Session->setFlash('No tiene permisos para realizar esta acción', 'flash_failure');
            $this->redirect('/exchanges/own');
            return;
        }
        $result = $this->Exchange->finalize($exchange);
        $this->Session->setFlash('El intercambio ha finalizado', 'flash_success');
        $this->redirect('/exchanges/own');
    }

    function delete($eid) {
        //borrar completamente. Solo para los post que son CRAP.
        //para lo demas usar finalize.
        if (!$this->Auth->user('admin')) {
            $this->getBack('No tiene permisos para realizar esta acción', 'flash_failure');
            return;
        }
        $exchange = $this->Exchange->read(null, $eid);
        $user_id = $exchange['Exchange']['user_id'];
        //delete all photos
        if (!empty($exchange['Exchange']['photos'])) {
            foreach ($exchange['Exchange']['photos'] as $photo) {
                $this->Exchange->deletePhoto($eid, $photo['id'], $this->uid);
            }
        }
        $this->Exchange->delete($eid);
        $this->redirect('/users/view/' . $user_id);
    }

    // admin sections
    function admin_index() {
        $exchanges = $this->Exchange->find('all', array(
            'limit' => 500
        ));
        $count = $this->Exchange->find('count');
        $countOffer = $this->Exchange->find('count', array(
            'conditions' => array(
                'exchange_type_id' => Configure::read('ExchangeType.Offer')
            )
        ));
        $countRequest = $this->Exchange->find('count', array(
            'conditions' => array(
                'exchange_type_id' => Configure::read('ExchangeType.Request')
            )
        ));
        $usersActive = $this->User->find('count', array(
            'conditions' => array(
                'active' => 1
            )
        ));
        $exchangesByUser = round($count / $usersActive, 2);
        $offersByUser = round($countOffer / $usersActive, 2);
        $requestsByUser = round($countRequest / $usersActive, 2);
        $this->set(compact('exchanges', 'count', 'countOffer', 'countRequest', 'exchangesByUser', 'offersByUser', 'requestsByUser'));
    }

    function view_photo() {
        $this->layout = 'popup';
        $this->set(array(
            'url' => $this->params['url']['photo_url'],
            'width' => $this->params['url']['width'],
            'height' => $this->params['url']['height'],
            'title_for_layout' => 'Foto'
        ));
    }

    private function _cantEditExchange($exchange) {
        return!$this->Auth->user('admin') && $exchange['Exchange']['user_id'] != $this->Auth->user('id');
    }

    function rate($valoration,$eid){
        $this->RequestHandler->respondAs('json');
        $exchange = $this->Exchange->read(null, $eid);

        if(!$this->Auth->user('id')){
            $this->result(false, 'Solo los usuarios registrados pueden valorar las publicaciones');
            return;
        }

        if($exchange['Exchange']['user_id']==$this->Auth->user('id')){
            $this->result(false, 'No puedes valorar tus propias pulicaciones.');
            return;
        }

        $rates = isset($exchange['Exchange']['rates'])?$exchange['Exchange']['rates']:array();
        if($valoration == "positive"){
            $rates[$this->Auth->user('id')] = 1;
        }else if($valoration == "negative"){
            $rates[$this->Auth->user('id')] = 0;
        }else{
            $this->result(false,'Error');
            return;
        }

        $exchange['Exchange']['rates'] =  $rates;
        $this->Exchange->save($exchange);

        $data = $this->Exchange->getTotalRates($exchange);
        $this->result(true, '', compact('data'));
      }

    // TODO: chequear si hay una mejor forma de implementar el API
    // probablemente usando los métodos ya existentes, para unificar
    // ambas cosas
    function api_index() {
      // TODO: listar posts paginados desde el cliente
    }

    // TODO: solo aceptar POST
    function api_create() {
        $this->autoRender = false;
        $result = $this->add_exchange(EXCHANGE_OFFER, '¡La oferta fue publicada!', 1);

        if ($result && isset($_FILES["photo"])) {
            // Subimos la foto
            $this->data['Photo'] = array(
                'exchange_id' => $this->Exchange->id
            );
            $this->uid = "1";
            $this->add_photo();
        }

        $response = array("success" => !!$result);
        if (Configure::read("debug")) {
            $response["errors"] = $this->Exchange->validationErrors;
        }
        echo json_encode($response);
    }
}

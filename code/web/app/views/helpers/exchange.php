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
class ExchangeHelper extends AppHelper {
	var $helpers = array('Html');

	function defaultPhoto($e,$size='square') {
		$url = $this->defaultPhotoUrl($e,$size);
		if ($url) {
			return $this->Html->image($url);
		} else {
			return $this->Html->image(DEFAULT_EXCHANGE_PHOTO, array('width'=>50, 'height'=>50));
		}
	}

	function defaultPhotoUrl($e, $size = 'square') {
		if (!isset($e['Exchange']['photos'])) {
			return false;
		}

		foreach ($e['Exchange']['photos'] as $photo) {
			if (@$photo['default']) {
				return $photo[$size]['url'];
			}
		}
	}
    
    /*
     * Devuelve el tipo del intercambio como string
     */
    function type($exchange) {
        switch($exchange['Exchange']['exchange_type_id']) {
            case EXCHANGE_REQUEST:
                return "Pedido";
                break;
            case EXCHANGE_OFFER:
                return "Oferta";
                break;
            case EXCHANGE_EVENT:
                return "Evento";
                break;
            case EXCHANGE_SERVICE:
                return "Servicio";
                break;
            default:
                echo "Tipo desconocido";
        }
    }
    
    /*
     * Devuelve la clase css para ese tipo.
     */
    function cssClass($exchange) {
          switch($exchange['Exchange']['exchange_type_id']) {
            case EXCHANGE_REQUEST:
                return "request";
                break;
            case EXCHANGE_OFFER:
                return "offer";
                break;
            case EXCHANGE_EVENT;
                return "event";
                break;
            case EXCHANGE_SERVICE;
                return "service";
                break;
        }
    }
    
    function is_service($exchange) {
        return $this->is($exchange, EXCHANGE_SERVICE);
    }
    
    function is_event($exchange) {
        return $this->is($exchange, EXCHANGE_EVENT);
    }
    
    function is_offer($exchange) {
        return $this->is($exchange, EXCHANGE_OFFER);
    }
    
    function is_request($exchange) {
        return $this->is($exchange, EXCHANGE_REQUEST);
    }
    
    function is($exchange, $exchange_type_id) {
        return $exchange['Exchange']['exchange_type_id'] == $exchange_type_id;
    }
    
    //formatea una fecha como se guarda en MongoDB en un formato razonable.
    function date() {
        
    }
}

?>

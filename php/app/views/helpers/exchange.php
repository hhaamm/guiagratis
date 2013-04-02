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

	function defaultPhoto($e, $size='square') {
		$url = $this->defaultPhotoUrl($e, $size);
		if ($url) {
			return $this->Html->image($url, array('width' => 50, 'height' => 50));
		} else {
			return $this->Html->image(DEFAULT_EXCHANGE_PHOTO, array('width'=>50, 'height'=>50));
		}
	}

	function defaultPhotoUrl($e, $size = 'square') {
		if (!isset($e['Photo'])) {
			return false;
		}

		foreach ($e['Photo'] as $photo) {
			if ($photo['is_default']) {
				return $this->imageUrl($photo['file_name'], $size);
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

    function ubicacion($exchange) {
	    if (empty($exchange['Exchange']['country']))
		    return "Ubicación desconocida";

	    $ubicacion = $exchange['Exchange']['country'];

	    if (empty($exchange['Exchange']['province'])) {
		    return $ubicacion;
	    }

	    $ubicacion .= ', '.$exchange['Exchange']['province'];

	    if (empty($exchange['Exchange']['locality'])) {
		    return $ubicacion;
	    }

	    $ubicacion .= ', '.$exchange['Exchange']['locality'];
	    
	    return $ubicacion;
    }

    /**
     * Toma el nombre de una foto o imágen y la formatea según uno de los formatos.
     *
     * @param imgFile Nombre de la imágen original.
     * @param imgSize Size de la imágen (square, small, etc.) 
     *
     * @return String
     */
    function imageUrl($imgFile, $imgSize) {
        $pathinfo = pathinfo($imgFile);
        return '/uploads/'.$pathinfo['filename'].'_'.$imgSize.'.'.$pathinfo['extension'];
    }
}

?>

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

//este shell se fija en todos los exchanges si tienen asignados una ubicación
//(country, province y locality)
//si no tienen ninguna, llama a Juan Carlos Google Maps y trata de obtener
//dicha información basándose en la latitud y longitud.
class UpdateLocationShell extends Shell {
        var $uses = array('Exchange');

        function main() {
                $exchanges = $this->Exchange->find('all', array('limit'=>10000));

                foreach($exchanges as $e) {
                        if (empty($e['Exchange']['country']) && empty($e['Exchange']['province']) && empty($e['Exchange']['locality'])) {
                                $lat = $e['Exchange']['lat'];
                                $lng = $e['Exchange']['lng'];

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
				
				$e['Exchange']['country'] = $country;
				$e['Exchange']['locality'] = $locality;
				$e['Exchange']['province'] = $province;

				$this->Exchange->save($e);
                        }
                }
        }
}
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
class GeoComponent extends Object {
    //Source: http://www.zipcodeworld.com/samples/distance.php.html
    function distance($lat1, $lon1, $lat2, $lon2, $unit = 'K') {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    //Makes a call to Google Maps to get the address matching some string
    function getMatchingPlaces($string) {
        /*if (Cache::read($this->matchingPlacesKey($string))) {
            return Cache::read($this->matchingPlacesKey($string));
        }*/

        $url = "http://maps.google.com/maps/geo?"; // base URL
        $url .= "q=" . urlencode($string);
        $url .= "&output=json";
        $url .= "&key=".Configure::read('GoogleMap.ApiKey');
        $obj = $this->getJSON($url);
	
	$results = array();
	foreach ($obj->Placemark as $location) {
	    $results[] = array('address'=>$location->address, 'longitude'=>$location->Point->coordinates[0], 'latitude'=>$location->Point->coordinates[1]);
	}
	
        Cache::write($this->matchingPlacesKey($string), $results);

        return $results;
    }

    //Returns a point from an ip
    //TODO: make a donation to this site.
    function localizeFromIp() {
        $ip = $this->findIp();
        $url = "http://ipinfodb.com/ip_query.php?ip=$ip&output=json&timezone=false";
        $obj = $this->getJSON($url);
        $lat = $obj->Latitude;
        $lng = $obj->Longitude;
        return array('latitude' => $lat, 'longitude' => $lng);
    }

    //This function make two calls so it slower than localizeFromIp but is more accurate
    //If cache is enabled, this function is really fast
    function localizeFromIpTwo() {
        //TODO: revisar este método
        return Configure::read('GoogleMaps.DefaultPoint');
        $ip = $this->findIp();

        if (Cache::read($this->ipCacheKey($ip))) {
            $obj = Cache::read($this->ipCacheKey($ip));
        } else {
			$api_key = Configure::read('IPInfoDB.ApiKey');
			$url = "http://api.ipinfodb.com/v2/ip_query.php?key=$api_key&ip=$ip&timezone=false&output=json";
            $obj = $this->getJSON($url);
            Cache::write($this->ipCacheKey($ip), $obj);
        } 

        $gmap_query = $obj->City.", ".$obj->RegionName.", ".$obj->CountryName;
        if (Cache::read($this->locationCacheKey($gmap_query))) {
            $obj = Cache::read($this->locationCacheKey($gmap_query));
        } else {
            $url = "http://maps.google.com/maps/geo?"; // base URL
            $url .= "q=" . urlencode($gmap_query);
            $url .= "&output=json";
            $url .= "&key=".Configure::read('GoogleMap.ApiKey');
            $obj = $this->getJSON($url);
            Cache::write($this->locationCacheKey($gmap_query), $obj);
        } 

        $lat= $obj->Placemark[0]->Point->coordinates[1];
        $lng= $obj->Placemark[0]->Point->coordinates[0];
        return array('latitude' => $lat, 'longitude' => $lng);
    }

    function ipCacheKey($ip) {
        return 'geolocation-'.$ip;
    }

    function locationCacheKey($location) {
        return 'geolocation-'.$location;
    }

    function matchingPlacesKey($string) {
        return 'matching-places-'.$string;
    }

    function findIp() {
        if(getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        elseif(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else
            $ip = getenv("REMOTE_ADDR");
        return $ip == "127.0.0.1" ? Configure::read('GeoTestIpAddress') : $ip;
    }

    function getJSON($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $content = curl_exec($ch);

        if (empty($content)) throw new Exception("Unable to get content from $url");
        //$result = htmlspecialchars($content);
        $obj = json_decode($content);
        return $obj;
    }
}
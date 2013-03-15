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
class ImagesController extends AppController {
	var $uses = array();
	var $allowed_types = array('jpg','jpeg','gif','png','bmp');

	function upload() {
		$this->autoRender = false;

		//TODO: devuelve un html que es agregado a cierto div
		//TODO: reemplazar los die con métodos del ajax_messages component
		if (empty($_FILES)) {
			die("Falló la subida");
		}
		$photo = $_FILES['photo'];

		if (empty($this->data['Photo']['prefix'])) {
			die("Falló la subida");
		}

		$upload_dir = WWW_ROOT.DS.'uploads'.DS;
		$file_extension = file_extension($photo['name']);

		if (!in_array($file_extension,$this->allowed_types)) {
			die("Solo están permitidas las extensiones de archivo: ".implode(',',$this->allowed_types));
		}

		$new_image_name = $this->uid."_".$this->data['Photo']['prefix']."_".time().".".$file_extension;

		if (move_uploaded_file($_FILES['photo']['tmp_name'],$upload_dir.$new_image_name)) {
			$new_image_url = '/uploads/'.$new_image_name;
			echo "<img src='$new_image_url'>";
		} else {
			die("Falló la subida");
		}
	}
}
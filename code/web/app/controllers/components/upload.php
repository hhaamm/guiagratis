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

/*
 * This class manage all uploads. Makes validation, resizes images, etc.
*/
class UploadComponent extends Object {

	var $defaultOpts = array(
		'images'=>array(),
		'file_field'=>'file',
		'url_path'=>'',
		'dest_path'=>WWW_ROOT,
		'delete_original'=>true
	);

	function initialize(&$controller, $settings = array()) {
		//TODO: override defaults here.
	}

	/*
     * Uploads an image and make some resizes to it, 'a pedido'.
     *
     * You can call this method in this way from your Controller:
     *
     * $this->Upload->images(array('images' => array(
     *     'square' => array('width' => 50, 'height' => 50),
     *     'small' => array('width' => 200, 'keep_aspect_ratio' => true),
     *     'distorted' => array('width' => 500, 'height' => 200, 'keep_aspect_ratio' => false)
     * )),
     * 'file_field' => 'image', 'dest_path' => WWW_ROOT.'img'.DS.'uploaded')
	*/
	function images($options = array()) {
		$options = array_merge($this->defaultOpts, $options);
		
		$ext = $this->imageExt($options);
		if (isset($options['allowed_types'])) {
			if (!$this->isAllowed($ext, $options['allowed_types'])) {
				throw new Exception(sprintf(__("%s is not an allowed type",true), $ext));
			}
		}
		
		//Copy the file to some folder
		$file = $this->file($options);
		
		foreach ($options['images'] as $imgName => $imageOpt) {
			$new_file_name = uniqid().'.'.$ext;
			$new_file_path = $options['dest_path'].$new_file_name;

			copy($file['file_path'],$new_file_path);
			$this->resizeImage($new_file_path, $imageOpt);
			
			$result[$imgName] = array('file_path' => $new_file_path, 'url' => '/uploads/'.$new_file_name, 'file_name' => $new_file_name);
		}

		//Deleting original file
		if (isset($options['delete_original'])) {
			unlink($file['file_path']);
		} else {
			//TODO: test
			$result['original'] = array('file_path'=>$file['file_path'], 'url'=>'/uploads/'.$file['file_name'], 'file_name'=>$file['file_name']);
		}

		return $result;
	}

	//Returns true or false if the image needs a resize.
	//Changes width and height if neccesary
	//TODO: poner todo esto en el componente de imágen!
	function needResize($imageFile, &$imageOpt) {
		//TODO: check this!
		if (isset($imageOpt['resize']) && $imageOpt['resize'] == false)
			return false;

		if (isset($imageOpt['maxWidth']) && imagesx($imageFile) > $imageOpt['maxWidth']) {
			$imageOpt['width'] = $maxWidth;
		}
		if (isset($imageOpt['maxHeight']) && imagesy($imageFile) > $imageOpt['maxHeight']) {
			$imageOpt['height'] = $maxHeight;
		}

		if (isset($imageOpt['width']) && isset($imageOpt['height'])) {
			return true;
		}
		return false;
	}

	/*
     * Uploads a file.
     * Params in options:
     *
     * file_field: form's field name. Must be a single name (like 'file' or 'image'). Can't be something like data[User][image]
     * file_path: folder where the file will be upload to.
     * url_path: url where the files will be accesible.
     *
     * Use example:
     * $upload_result = $this->Upload->file(array(
     *	   'file_field' => 'image',
     *	   'file_path' => WWW_ROOT.'img'.DS.'uploaded',
     *     'url_path' => '/img/uploaded')
     * );
	*/
	function file($options = array()) {
		$options = array_merge($this->defaultOpts,$options);
		$fieldName = $options['file_field'];
		
		$ext = $this->file_extension($_FILES[$fieldName]['name']);
		$fileName = uniqid().'.'.$ext;
		$filePath = $options['dest_path'];
		$this->validateDirectory($filePath);
		$fileFullPath = $filePath.DS.$fileName;
		$origin = $_FILES[$fieldName]['tmp_name'];
		
		is_uploaded_file($origin)
				or debug("'$origin' is not an HTTP upload");
		move_uploaded_file($origin, $fileFullPath)
				or debug("Error moving '$origin' to '$fileFullPath'");
		$urlPath = $options['url_path'];
		$urlFullPath = $urlPath.'/'.$fileName;
		
		return array('file_path' => $fileFullPath, 'url' => $urlFullPath, 'file_name' => $fileName);
	}

	//Image functions
	function resizeImage($imagePath, $options = array('width' => 50, 'height' => 50, 'keep_aspect_ratio' => true)) {
		App::import('Component', 'Image');

		$thumb=new thumbnail($imagePath, $options);
		$thumb->jpeg_quality(100);
		$thumb->save($imagePath);
	}

	function resizeIfNeccesary($imagePath, $options) {
		throw new Exception("Implement this function");
	}

	//Util functions
	function file_extension($filename) {
		$path_info = pathinfo($filename);
		return $path_info['extension'];
	}

	function validateDirectory($path) {
		if (!is_dir($path))
			debug("$path is not a directory");
	}

	/*
     * Returns true or false, depending of the file extension passed.
     * ext: file extension.
     * allowedTypes: a list of file extensions.
	*/
	function isAllowed($ext, $allowedTypes) {
		foreach ($allowedTypes as $allowedType) {
			if (strtolower($allowedType) == strtolower($ext)) {
				return true;
			}
		}
		return false;
	}

	function imageExt($opts) {
		$fieldname = $opts['file_field'];
		$path_info = pathinfo($_FILES[$fieldname]['name']);
		$ext = $path_info['extension'];
		return $ext;
	}
}
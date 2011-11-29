<?php

class ImageComponent extends Object {
    var $allowed_types = array('jpg','gif','bmp','png');

    //TODO: delete unneccesary functions
    function getFileExtension($str) {
	$i = strrpos($str,".");
	if (!$i) {
	    return "";
	}
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
    }

    function getGroupImagePath($group_id, $extension = 'jpg') {
	$ext_sep = '.';

	if (empty($extension))
	    $ext_sep = '';

	return Configure::read('GroupIconFolder').$group_id.$ext_sep.$extension;
    }

    //If no extension provided, this method will search for the allowed types.
    function getGroupImageUrl($group_id, $extension = null) {
	if (!$extension) {
	    $extension = $this->getFileNameExtension($this->getGroupImagePath($group_id, ''));
	    
	    if (empty($extension))
		throw new Exception(sprintf(__("Couldn't find valid file in '%s'",true),"/img/group_icon/$group_id"));
	}

	return "/img/group_icon/$group_id.$extension";
    }

    private function getFileNameExtension($filefullpath) {
	foreach ($this->allowed_types as $allowed_type) {
	    $file_name = "$filefullpath.$allowed_type";

	    if (file_exists($file_name)) {
		//debug('Group image found: '.$file_name);
		return $allowed_type;
	    }
	}
	return false;
    }

    function deleteGroupImage($group_id) {
	foreach ($this->allowed_types as $allowed_type) {
	    $file_name = $this->getGroupImagePath($group_id, $allowed_type);
	    if (file_exists($file_name)) {
		//debug('File exists! Deleting it');
		unlink(($file_name));
	    }
	}
    }

    function resizeImg($imgname, $size) {
	$thumb=new thumbnail($imgname);
	$thumb->size_height($size);
	$thumb->jpeg_quality(100);
	$thumb->save($imgname);
    }
}


##############################################
# Shiege Iseng Resize Class
# 11 March 2003
# shiegege_at_yahoo.com
# View Demo :
#   http://shiege.com/scripts/thumbnail/
/*############################################
Sample :
$thumb=new thumbnail("./shiegege.jpg");			// generate image_file, set filename to resize
$thumb->size_width(100);				// set width for thumbnail, or
$thumb->size_height(300);				// set height for thumbnail, or
$thumb->size_auto(200);					// set the biggest width or height for thumbnail
$thumb->jpeg_quality(75);				// [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
$thumb->show();						// show your thumbnail
$thumb->save("./huhu.jpg");				// save your thumbnail to file
----------------------------------------------
Note :
- GD must Enabled
- Autodetect file extension (.jpg/jpeg, .png, .gif, .wbmp)
  but some server can't generate .gif / .wbmp file types
- If your GD not support 'ImageCreateTrueColor' function,
  change one line from 'ImageCreateTrueColor' to 'ImageCreate'
  (the position in 'show' and 'save' function)
*/############################################


class thumbnail {
    var $img;
    var $opts;
    var $src;
    var $transparentExt = array('gif', 'png');

    function saveTransparency($image_resized, $image, $type) {
	if (in_array(strtolower($type), $this->transparentExt)) {
	    $trnprt_indx = imagecolortransparent($image);

	    // If we have a specific transparent color
	    if ($trnprt_indx >= 0) {

		// Get the original image's transparent color's RGB values
		$trnprt_color    = imagecolorsforindex($image, $trnprt_indx);

		// Allocate the same color in the new image resource
		$trnprt_indx    = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

		// Completely fill the background of the new image with allocated color.
		imagefill($image_resized, 0, 0, $trnprt_indx);

		// Set the background color for new image to transparent
		imagecolortransparent($image_resized, $trnprt_indx);


	    }
	    // Always make a transparent background color for PNGs that don't have one allocated already
	    elseif (strtolower($type) == 'png') {

		// Turn off transparency blending (temporarily)
		imagealphablending($image_resized, false);

		// Create a new transparent color for image
		$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);

		// Completely fill the background of the new image with allocated color.
		imagefill($image_resized, 0, 0, $color);

		// Restore transparency blending
		imagesavealpha($image_resized, true);
	    }
	}
    }

    function thumbnail($imgfile, $opts = array()) {
        $this->opts = $opts;
        $this->src = $imgfile;

	$this->img["format"]=ereg_replace(".*\.(.*)$","\\1",$imgfile);
	$this->img["format"]=strtoupper($this->img["format"]);
	if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
	    //JPEG
	    $this->img["format"]="JPEG";
	    $this->img["src"] = ImageCreateFromJPEG ($imgfile);
	} elseif ($this->img["format"]=="PNG") {
	    //PNG
	    $this->img["format"]="PNG";
	    $this->img["src"] = ImageCreateFromPNG ($imgfile);
	} elseif ($this->img["format"]=="GIF") {
	    //GIF
	    $this->img["format"]="GIF";
	    $this->img["src"] = ImageCreateFromGIF ($imgfile);
	} elseif ($this->img["format"]=="WBMP") {
	    //WBMP
	    $this->img["format"]="WBMP";
	    $this->img["src"] = ImageCreateFromWBMP ($imgfile);
	} else {
	    //DEFAULT
	    echo "Not Supported File";
	    exit();
	}
	@$this->img["lebar"] = imagesx($this->img["src"]);
	@$this->img["tinggi"] = imagesy($this->img["src"]);

	//default quality jpeg
	$this->img["quality"]=75;
    }

    function size_height($size=100) {
	//height
	$this->img["tinggi_thumb"]=$size;
	@$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
    }

    function size_width($size=100) {
	//width
	$this->img["lebar_thumb"]=$size;
	@$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
    }

    function size_auto($size=100) {
	//size
	if ($this->img["lebar"]>=$this->img["tinggi"]) {
	    $this->img["lebar_thumb"]=$size;
	    @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
	} else {
	    $this->img["tinggi_thumb"]=$size;
	    @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
	}
    }

    function jpeg_quality($quality=75) {
	//jpeg quality
	$this->img["quality"]=$quality;
    }

    function show() {
	//show thumb
	@Header("Content-Type: image/".$this->img["format"]);

	/* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
	$this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
	@imagecopyresized ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

	if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
	    //JPEG
	    imageJPEG($this->img["des"],"",$this->img["quality"]);
	} elseif ($this->img["format"]=="PNG") {
	    //PNG
	    imagePNG($this->img["des"]);
	} elseif ($this->img["format"]=="GIF") {
	    //GIF
	    imageGIF($this->img["des"]);
	} elseif ($this->img["format"]=="WBMP") {
	    //WBMP
	    imageWBMP($this->img["des"]);
	}
    }

    //Get width and height for this immage
    //Returns true if the image must be resized and false if not.
    function calculateWidthHeight() {
        if (isset($this->opts['resize']) && $this->opts['resize'] == false) {
            $this->img['lebar_thumb'] = $this->img['lebar'];
            $this->img['tinggi_thumb'] = $this->img['tinggi'];
            return false;
        }

        $haveMax = isset($this->opts['maxHeight']) || isset($this->opts['maxWidth']);
        if ($haveMax) {
            $shouldResize = $this->img['lebar'] > @$this->opts['maxWidth'] || $this->img['tinggi'] > @$this->opts['maxHeight'];
            if ($shouldResize) {
                $max = $this->opts['maxHeight'] < $this->opts['maxWidth'] ? $this->opts['maxHeight'] : $this->opts['maxWidth'];
                $this->size_auto($max);
            } else {
                $this->img['lebar_thumb'] = $this->img['lebar'];
                $this->img['tinggi_thumb'] = $this->img['tinggi'];
                return false;
            }
            return true;
        }

        //If user has set width and height, we set that
        if (isset($this->opts['width']) && isset($this->opts['height'])) {
            $this->img['lebar_thumb'] = $this->opts['width'];
            $this->img['tinggi_thumb'] = $this->opts['height'];
        } else if (isset($this->opts['width'])) {
            $this->size_width($this->opts['width']);
        } else if (isset($this->opts['height'])) {
            $this->size_height($this->opts['height']);
        }
        return true;
    }

    function save($save="") {
        $resize = $this->calculateWidthHeight();

        if  (!$resize) {
            if (!file_exists($save)) 
                copy($this->src, $save);
            return true;
        }

	//save thumb
	if (empty($save)) $save=strtolower("./thumb.".$this->img["format"]);
	/* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
	$this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
	$this->saveTransparency($this->img['des'], $this->img["src"], $this->img['format']);
	@imagecopyresized ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

	if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
	    //JPEG
	    imageJPEG($this->img["des"],"$save",$this->img["quality"]);
	} elseif ($this->img["format"]=="PNG") {
	    //PNG
	    imagePNG($this->img["des"],"$save");
	} elseif ($this->img["format"]=="GIF") {
	    //GIF
	    imageGIF($this->img["des"],"$save");
	} elseif ($this->img["format"]=="WBMP") {
	    //WBMP
	    imageWBMP($this->img["des"],"$save");
	}
    }
}


?>

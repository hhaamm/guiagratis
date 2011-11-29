<?php

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
}

?>

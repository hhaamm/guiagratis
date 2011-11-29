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
}

?>

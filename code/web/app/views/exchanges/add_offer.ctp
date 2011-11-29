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
?>
<?php
	$javascript->link("http://maps.google.com/maps?file=api&v=2&key=".Configure::read('GoogleMaps.ApiKey'), false);
?>
<fieldset>
	<legend>Agregar una oferta</legend>
	<?php
	echo $this->Form->create();
	echo $this->Form->input('title',array('label'=>'Título'));
	echo $this->Form->input('detail',array('label'=>'descripción'));
	echo $this->element('set_exchange_location');
	echo $this->Form->end('Agregar oferta');
	?>
</fieldset>
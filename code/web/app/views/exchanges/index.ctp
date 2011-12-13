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
?>
<?php
	$javascript->link("http://maps.google.com/maps?file=api&v=2&key=".Configure::read('GoogleMaps.ApiKey'), false);
	echo $this->element('gmap_default_values', array('start_point' => $start_point));
	$javascript->link('jquery.jec-1.2.5', false);
	$javascript->link('maps.google.polygon.containsLatLng', false);
	$javascript->link('gmap', false);
	$javascript->link('exchanges_index', false);
	$javascript->link('google_maps_circle_overlay', false);
	?>

<div class="left-menu">
	<p>
		¡Buscá cosas gratis que necesites! ¡Regalá cosas que te sobren!
	</p>
	<fieldset>
		<legend>Ir a</legend>
		<?php
		echo $this->Form->create(array('onsubmit'=>'change_location(); return false;'));
		echo $this->Form->input('location',array('label'=>'Dirección', 'id' => 'location'));
		//echo $this->Form->input('save_as_default',array('type'=>'checkbox','label'=>'Guardar ubicación como predeterminada'));
		echo $this->Form->end('Cambiar ubicación');
		?>
	</fieldset>
	<fieldset>
		<legend>Búsqueda</legend>
		<?php
		echo $this->Form->create('Search');
        echo $this->Form->input('text', array('label'=>'Tags', 'id'=>'text_tags'));
		echo $this->Form->input('exchange_type_id',array('options'=>array(
			Configure::read('ExchangeType.Request')=>'Pedidos',
			Configure::read('ExchangeType.Offer')=>'Ofertas',
			Configure::read('ExchangeType.All')=>'Todos'
		),
			'id'=>'exchange_type_id',
			'label'=>'Ver',
            'default'=>Configure::read('ExchangeType.All')));
		echo $this->Form->hidden('lat',array('id'=>'lat'));
		echo $this->Form->hidden('lng',array('id'=>'lng'));
		echo $this->Form->end();
		?>
	</fieldset>
</div>
<div class="content">
	<div id="map"></div>
</div>
<div class="br"></div>

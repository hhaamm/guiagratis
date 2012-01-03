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
	$this->Javascript->link("http://maps.google.com/maps?file=api&v=2&key=".Configure::read('GoogleMaps.ApiKey'), false);
    $this->Javascript->link('tinymce/tiny_mce.js', false);
?>
<!-- TinyMCE -->
<script type="text/javascript">
    $(function() {
        tinyMCE.init({
            mode : "textareas",
            theme : "simple"
        });
    });
    setLocationIcon = get_custom_icon('O', 'FF9305', 'FFFFFF', false);
</script>
<!-- /TinyMCE -->
<fieldset>
	<legend>Agregar una oferta</legend>
	<?php
    echo $this->Form->create(null,array("onsubmit"=>"return markerMoved();"));
	echo $this->Form->input('title',array('label'=>'Título'));
	echo $this->Form->input('detail',array('label'=>'descripción', 'type'=>'textarea'));
    echo $this->Form->input('tags', array('label'=>'Tags (separados por coma)'));
	echo $this->element('set_exchange_location');
	echo $this->Form->end('Agregar oferta');
	?>
</fieldset>
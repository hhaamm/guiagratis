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
</script>

<div style="float: right;">
    <?php
        $icon =  $this->Html->image('/img/icons/photo.png');
        echo $this->Html->link($icon.' Editar fotos',array('controller'=>'exchanges','action'=>'edit_photos',$this->data['Exchange']['id']),array('class'=>"link-button", 'escape' => false));
    ?>
</div>
        
<?php

if ($current_user['User']['admin']) { ?>
<p>Creado por <?php echo $this->User->link($creator); ?></p>
<?php 
}

echo $this->Form->create('Exchange');
echo $this->Form->hidden('id');
echo $this->Form->input('title',array('label'=>'Título'));
if ($this->Exchange->is_service($this->data)) {
    echo $this->Form->input('hours_of_opening',array('label'=>'Horario de atención'));
}
if ($this->Exchange->is_event($this->data)) {
    echo $this->Form->input('start_date',array('label'=>'Fecha de inicio', 'type'=>'datetime', 'timeFormat'=>24));
    echo $this->Form->input('end_date',array('label'=>'Fecha de finalización', 'type'=>'datetime', 'timeFormat'=>24));
}
echo $this->Form->input('detail',array('label'=>'Descripción'));
echo $this->Form->input('tags', array('label'=>'Tags (separados por coma)'));
echo $this->element('set_exchange_location');

echo $this->Form->end('Guardar cambios');

echo $this->Html->link('Volver','/exchanges/view/'.$this->data['Exchange']['id']);

?>
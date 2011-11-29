<?php
	$javascript->link("http://maps.google.com/maps?file=api&v=2&key=".Configure::read('GoogleMaps.ApiKey'), false);
?>
<?php

echo $this->Form->create('Exchange');
echo $this->Form->hidden('_id');
echo $this->Form->input('title',array('label'=>'Título'));
echo $this->Form->input('detail',array('label'=>'Descripción'));
echo $this->element('set_exchange_location');

echo $this->Form->end('Guardar cambios');

echo $this->Html->link('Volver','/exchanges/view/'.$this->data['Exchange']['_id']);

?>
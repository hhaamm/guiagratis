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
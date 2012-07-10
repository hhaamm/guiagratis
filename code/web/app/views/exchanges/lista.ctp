<div id="exchanges-list-page">

<div id="filters">
<?php
echo $this->Html->link('mostrar filtros', '#', array('id'=>'toggle_filtros'));
echo $this->Form->create('Filter', array('id'=>'filters_form', 'url'=>$this->Html->url($this->here)));

echo $this->Form->input('query', array('label'=>'Búsqueda'));

echo $this->Form->input('exchange_type', array('multiple'=>'checkbox', 'options'=>Configure::read('ExchangeType.Names'), 'label'=>'Publicaciones'));

echo $this->Form->end('Filtrar');
?>
<div class="clear"></div>
</div>

<ul id="exchanges">
<?php foreach ($exchanges as $e) { ?>
        <li>
	<h3><?php echo $this->Html->link($e['Exchange']['title'], '/exchanges/view/'.$e['Exchange']['_id']); ?></h3>
	 <?php 
	 if ($this->Exchange->defaultPhotoUrl($e)) {
		 echo $this->Exchange->defaultPhoto($e); 
	 }
         ?>
<p class="datos-adicionales">
		  <span class="tipo "><strong><?php echo Configure::read('ExchangeType.Names.'.$e['Exchange']['exchange_type_id']); ?></strong></span> 
		  <span class=""><?= $this->Exchange->ubicacion($e); ?></span>
		  </p>
<p class="datos-adicionales">Publicado <?php echo $this->Time->timeAgoInWords($e['Exchange']['created'])?> por <?= $this->Html->link($e['Exchange']['username'], array('controller'=>'users', 'action'=>'view', $e['Exchange']['user_id'])) ?></p>
	 <?php echo $e['Exchange']['detail']; ?>
<div class="links">
		 <?php echo $this->Html->link('+enviar mensaje', array('controller'=>'conversations', 'action'=>'add', $e['Exchange']['user_id'])); ?>
		 <?php echo $this->Html->link('+comentar', array('controller'=>'exchanges', 'action'=>'view', $e['Exchange']['_id'].'#comment')); ?>
</div>
	</li>
 <?php } ?>
</ul>
<div class="clear"></div>

<div class="paginator">
<?php

echo $paginator->prev('Anterior');
echo $paginator->numbers();
echo $paginator->next('Siguiente');
?>
</div>

</div>
<script type="text/javascript">
	$(document).ready(function() {
			$('a#toggle_filtros').click(function(e) {
					if ($(e.currentTarget).text() == 'mostrar filtros') {
						$(e.currentTarget).text('ocultar filtros');
						$('form#filters_form').show();
					} else {
						$(e.currentTarget).text('mostrar filtros');
						$('form#filters_form').hide();
					};
				})
				//.trigger('click');
			
		});
</script>
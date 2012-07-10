<div id="exchanges-list-page">

<div id="filters">
<?php
echo $this->Html->link('mostrar filtros', '#', array('id'=>'toggle_filtros'));
echo $this->Form->create('Filter', array('id'=>'filters_form', 'url'=>$this->Html->url($this->here)));

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
		 Publicado <?php echo $this->Time->timeAgoInWords($e['Exchange']['created'])?> por <?= $this->Html->link($e['Exchange']['username'], array('controller'=>'users', 'action'=>'view', $e['Exchange']['user_id'])) ?></p>
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

<?php
if(isSet($_GET['query'])){
 if(!empty($exchanges))
    foreach($exchanges as $e) { ?>
        <div  style="margin: 10px 0px;">
            <?php echo   $this->Html->link($this->Exchange->defaultPhoto($e),array('action'=>'view','controller'=>'exchanges',$e['Exchange']['_id']),array('escape'=>false , 'style' => 'float:left;margin-right: 10px;')) ?>
            <div style="float: left;">
             <p><?php echo Configure::read('ExchangeType.Names.'.$e['Exchange']['exchange_type_id']); ?>  - publicado  <?php echo $this->Time->timeAgoInWords($e['Exchange']['created'])?></p>
             <?php echo $this->Html->link($e['Exchange']['title'],array('action'=>'view','controller'=>'exchanges',$e['Exchange']['_id']),array('class'=>'exchange-view-link'))?>
            </div>
            <div class="clear"></div>
        </div>
<?php
    }else{
?>
      <div style="text-align: center;">
       <h2>La busqueda no produjo ningun resultado</h2>
      </div>
<?php
    }
  }
?>
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
				}).trigger('click');
			
		});
</script>
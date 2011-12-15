
<p>
    Perfil de usuario:  <?php echo $user['User']['username'] ?> (<?php echo $this->Html->link('PM','/conversations/add/'.$user['User']['_id']);?>)
</p>

<br/>
<br/>
<br/>

<h2> Pedidos y ofertas de este usuario </h2>

<ul class="exchanges">
<?php foreach($exchanges as $e) { ?>
	<li>
		<h3><?php echo $e['Exchange']['title']?> (publicado <?php echo $this->Time->timeAgoInWords($e['Exchange']['created'])?>)</h3>
		<?php echo $this->Exchange->defaultPhoto($e) ?>
		<p><?php echo Configure::read('ExchangeType.Names.'.$e['Exchange']['exchange_type_id']);?></p>
		<p><?php echo $this->Html->link('Ver','/exchanges/view/'.$e['Exchange']['_id'])?>
		<?php if ($e['Exchange']['state'] == EXCHANGE_FINALIZED) {
			echo " | ";
			echo "Intercambio finalizado el ".$this->Time->timeAgoInWords($e['Exchange']['finalize_time']);
		} else {
           if($current_user && $user['User']['_id'] == $current_user['User']['_id']){
			echo " | ";
			echo $this->Html->link('Editar','/exchanges/edit/'.$e['Exchange']['_id']);
			echo " | ";
			echo $this->Html->link('Finalizar','/exchanges/finalize/'.$e['Exchange']['_id'], null, "Una vez que finalizes el intercambio dejará de estar publicado. ¿Estás seguro?");
           }
		} ?>
		</p>
	</li>
<?php } ?>
</ul>

<br/>
<br/>
    
<?php #Debugger::dump($user['User']); ?>
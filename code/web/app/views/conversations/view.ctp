<div>
	<h3><?php echo $conversation['Conversation']['title']?></h3>

	<ul>
	<?php foreach ($conversation['Conversation']['messages'] as $message) { ?>

		<li>
			<div><?php echo $message['from_data']['username']." (".$this->Time->timeAgoInWords($message['created']).")" ?></div>
			<p><?php echo $message['text']?></p>
		</li>

	<?php }	?>
	</ul>

	<fieldset>
		<legend>Responder</legend>
		<?php
			echo $this->Form->create('Conversation',array('action'=>'answer'));
			echo $this->Form->hidden('_id',array('default'=>$conversation['Conversation']['_id']));
			echo $this->Form->input('text',array('label'=>'Mensaje','type'=>'textarea'));
			echo $this->Form->end('Responder');
		?>
	</fieldset>

	<a href="/conversations">Volver</a>
</div>
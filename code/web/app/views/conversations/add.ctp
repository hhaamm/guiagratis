<fieldset>
	<legend>Enviar mensaje privado</legend>
	<?php
		echo $this->Form->create('Conversation');
		echo $this->Form->hidden('to');
		echo $this->Form->input('title',array('label'=>'Título','name'=>'data[Conversation][title]'));
		echo $this->Form->input('text',array('label'=>'Mensaje','type'=>'textarea','name'=>'data[Conversation][messages][0][text]'));
		echo $this->Form->end('Enviar mensaje');
	?>
</fieldset>
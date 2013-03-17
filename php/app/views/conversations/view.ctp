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
<div>
	<h3><?php echo $conversation[0]['Message']['title']?></h3>

	<ul>
	<?php foreach ($conversation as $message) { ?>

		<li>
            <div><?php echo $this->Html->link($message['Sender']['username'],array('controller'=>'users','action'=>'view', $message['Sender']['id']),array('style'=>'text-decoration:none'))." (".$this->Time->timeAgoInWords($message['Message']['created']).")" ?></div>
			<p><?php echo $message['Message']['detail']?></p>
		</li>

	<?php }	?>
	</ul>

	<fieldset>
		<legend>Responder</legend>
		<?php
			echo $this->Form->create('Message',array('action'=>'answer', 'url' => '/conversations/answer'));
			echo $this->Form->input('detail', array('label'=>'Mensaje','type'=>'textarea'));
            echo $this->Form->hidden('receiver_id');
            echo $this->Form->hidden('thread_id');
			echo $this->Form->end('Responder');
		?>
	</fieldset>

	<a href="/conversations">Volver</a>
</div>
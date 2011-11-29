<ul class="conversations">
	<?php foreach ($conversations as $c) { ?>
	<li>
		<div>De <?php echo $c['Conversation']['from_data']['User']['username']?> para <?php echo $c['Conversation']['to_data']['User']['username']?></div>
		<div><?php echo $c['Conversation']['title']?></div>
		<div><?php echo $this->Html->link('View','/conversations/view/'.$c['Conversation']['_id']); ?></div>
	</li>
	<?php } ?>
</ul>
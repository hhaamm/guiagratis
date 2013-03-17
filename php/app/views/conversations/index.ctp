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
<ul class="conversations">
	<?php foreach ($conversations as $m) { ?>
	<li>
		<div>De <?php echo $m['Sender']['username']?> para <?php echo $m['Receiver']['username']?></div>
		<div><?php echo $m['Message']['title']?></div>
		<div><?php echo $this->Html->link('Ver', '/conversations/view/'.$m['Message']['id']); ?></div>
	</li>
	<?php } ?>
</ul>
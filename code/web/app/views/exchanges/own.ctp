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
			echo " | ";
			echo $this->Html->link('Editar','/exchanges/edit/'.$e['Exchange']['_id']);
			echo " | ";
			echo $this->Html->link('Finalizar','/exchanges/finalize/'.$e['Exchange']['_id'], null, "Una vez que finalizes el intercambio dejará de estar publicado. ¿Estás seguro?");
		} ?>
		</p>
	</li>
<?php } ?>
</ul>
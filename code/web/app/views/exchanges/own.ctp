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
        <div style="float: right;">
            <?php
             $icon = $this->Html->image('/img/icons/eye.png');
             echo $this->Html->link($icon.' Ver','/exchanges/view/'.$e['Exchange']['_id'],array('class'=>"link-button", 'escape' => false))?>
            <?php if ($e['Exchange']['state'] == EXCHANGE_FINALIZED) {
                echo $this->Html->div('link-button',
                        $this->Html->image('/img/icons/abort.png').
                        "Finalizado ".$this->Time->timeAgoInWords($e['Exchange']['finalize_time']),
                        array('style'=>'background-color:#DDDDDD'));
            } else {
                $icon = $this->Html->image('/img/icons/modify.png');
                echo $this->Html->link($icon.' Editar','/exchanges/edit/'.$e['Exchange']['_id'],array('class'=>"link-button", 'escape' => false));
                $icon = $this->Html->image('/img/icons/terminate.png');
                echo $this->Html->link($icon.' Finalizar','/exchanges/finalize/'.$e['Exchange']['_id'], array('class'=>"link-button", 'escape' => false), "Una vez que finalizes el intercambio dejará de estar publicado. ¿Estás seguro?");
            } ?>
		</div>
		<p><?php echo Configure::read('ExchangeType.Names.'.$e['Exchange']['exchange_type_id']);?></p>
	</li>
<?php } ?>
</ul>
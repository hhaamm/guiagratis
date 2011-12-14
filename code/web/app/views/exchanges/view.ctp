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

    echo $this->element('social_buttons');
?>
<div>
	<h2><?php echo $exchange['Exchange']['title']?></h2>
    <p>por <?php echo $owner['User']['username'] ?> </p>

	<!-- SHOW ONLY WHEN IS OWNER -->
    <?php if( !empty($user) && $user['User']['_id'] == $owner['User']['_id'] ){ ?>
	<div class="admin edit-exchange-menu">
	<?php
		echo $this->Html->link('Editar',array('controller'=>'exchanges','action'=>'edit',$exchange['Exchange']['_id']));
	?> 
	<?php
		echo $this->Html->link('Editar fotos',array('controller'=>'exchanges','action'=>'edit_photos',$exchange['Exchange']['_id']));
	?>
	</div>
    <?php } ?>
	<br>

	<p class="exchange-description"><?php echo $exchange['Exchange']['detail']?></p>
    <p class="exchange-comment-tags">Tags: <?php echo $exchange['Exchange']['tags'];?></p>
	<div class="exchange-photos">
	<?php foreach ($exchange['Exchange']['photos'] as $photo) {
		echo $this->Html->image($photo['small']['url']);
	} ?>
	</div>
	<ul class="exchange-comment-list">
		<?php
		if (isset($exchange['Exchange']['comments'])) { 
			foreach($exchange['Exchange']['comments'] as $comment) { ?>
			<li class="exchange_comment">
				<div style="float:left">
					<h4 class="exchange_comment_header"><?php echo $comment['username']." (".$this->Time->timeAgoInWords($comment['created'],true).")"?></h4>
					<p class="exchange_comment_text"><?php echo $comment['text'];?></p>
				</div>
				<div class="exchange_comment_user_info">
					<?php echo $this->Html->link('PM','/conversations/add/'.$current_user['User']['_id']);?>
				</div>
				<div class="clear"></div>
			</li>
		<?php }} ?>
	</ul>
    
    <table style="margin: 5px" cellspacing="5">
       <tr>
         <td>
          <!-- Google  -->
          <g:plusone size="medium"></g:plusone>
         </td>
         <td>
          <!-- Facebook -->
          <div class="fb-like" data-href="<?php echo Router::url($this->here, true); ?>" data-send="false" data-layout="button_count" data-width="130" data-show-faces="true" data-font="lucida grande"></div>
         </td>
       </tr>
       <tr>
         <td>
          <!-- Twitter -->
          <a href="https://twitter.com/share" class="twitter-share-button" data-lang="es">Tweet</a>
         </td>
         <td>
          <!-- Taringa -->
          <t:sharer data-layout="medium_counter"></t:sharer>
         </td>
       </tr>
    </table>
	
	<fieldset>
		<legend><?php if ($exchange['Exchange']['exchange_type_id'] == Configure::read('ExchangeType.Offer'))
                echo "¿Necesitás este artículo?";
                else
                    echo "¿Querés donar este artículo?"; ?></legend>

		<?php
		if ($current_user) {

			echo $this->Form->create('Exchange',array('action'=>'add_comment'));
			echo $this->Form->input('comment',array('type'=>'textarea','label'=>'Contanos'));
			echo $this->Form->hidden('_id',array('default'=>$exchange['Exchange']['_id']));
			echo $this->Form->end('Comentá');
		} else {
			echo "Tenés que ".$this->Html->link('loguearte','/users/login')." para poder responder. ";
			echo "Hacé click ".$this->Html->link('acá','/users/register')." para registrarte.";
		}

		?>
	</fieldset>

	<a href="/exchanges/own">Volver</a>
</div>
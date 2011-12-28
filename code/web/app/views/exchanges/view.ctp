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
    $this->Javascript->link('agile_carousel.alpha', false);
    $this->Html->css('agile_carousel.css', null, array('inline'=>false));
?>
<script type="text/javascript">
    function viewPhoto(url, imageId) {
        var img = $('#'+imageId);
        var options = 'toolbar=no, resizable=no, width='+img.width()+", height="+img.height()+", resizable=no, menubar=no, location=no";
        window.open('/exchanges/view_photo/?'+$.param({photo_url:url, width:img.width(), height:img.height()}), "", options);
    }
</script>
<div>
    <div class="exchange-type <?php echo $this->Exchange->cssClass($exchange); ?>">
    <?php echo $this->Exchange->type($exchange); ?>
    </div>
	<h2><?php echo $exchange['Exchange']['title']?></h2>
    <p>por <?php echo $this->Html->link($owner['User']['username'] ,'/users/view/'.$owner['User']['_id'], array('style'=> 'text-decoration: none;' ));  ?> </p>

	<!-- SHOW ONLY WHEN IS OWNER -->
    <?php if( !empty($current_user) && ($current_user['User']['_id'] == $owner['User']['_id'] || $current_user['User']['admin']) ){ ?>
	<div class="admin edit-exchange-menu" style="float: right;">
    <?php
        if ($exchange['Exchange']['state'] != EXCHANGE_FINALIZED) {
            $icon =  $this->Html->image('/img/icons/modify.png');
            echo $this->Html->link($icon.' Editar',array('controller'=>'exchanges','action'=>'edit',$exchange['Exchange']['_id']),array('class'=>"link-button", 'escape' => false));

            $icon =  $this->Html->image('/img/icons/photo.png');
            echo $this->Html->link($icon.' Editar fotos',array('controller'=>'exchanges','action'=>'edit_photos',$exchange['Exchange']['_id']),array('class'=>"link-button", 'escape' => false));

            $icon =  $this->Html->image('/img/icons/terminate.png');
            echo $this->Html->link($icon.' Finalizar','/exchanges/finalize/'.$exchange['Exchange']['_id'], array('class'=>"link-button", 'escape' => false) , "Una vez que finalizes el intercambio dejará de estar publicado. ¿Estás seguro?");
        }else{
            echo $this->Html->div('link-button',
                $this->Html->image('/img/icons/abort.png').
                " Finalizado",
                array('style'=>'background-color:#DDDDDD'));
        }
        if($current_user['User']['admin']){
            $icon =  $this->Html->image('/img/icons/erase.png');
            echo $this->Html->link($icon.' Borrar',array('controller'=>'exchanges','action'=>'delete',$exchange['Exchange']['_id']),array('class'=>"link-button", 'escape' => false),"Esta acción es irreversible. Usar solo para contenido basura(CRAP) \\n ¿Seguro que deseas continuar?");
        }
	?>
	</div>
    <div class="clear"></div>
    <?php } ?>
	<br>

	<p class="exchange-description"><?php echo $exchange['Exchange']['detail']?></p>

    <p class="exchange-comment-tags">
        <?php echo $this->Html->image('/img/icons/blue_tag.png') ?>
        Tags:
        <?php
            $tags = explode(',',$exchange['Exchange']['tags']);
            $tag_links = array();
            foreach($tags as $tag){
              $tag_links[] = $this->Html->link($tag,'/exchanges/search?type=0&mode=0&query='.trim($tag));
            }
            echo implode(' , ',$tag_links);
    ?></p>
    
    <?php if(!empty($exchange['Exchange']['photos'])){ ?>
     <script type="text/javascript">
         $(document).ready(function(){
            var data = [
                // Creamos un div exterior para poder extraerle el HTML. Por eso hay dos divs, pero el resultado devuelve sólo uno!
                // Generé el HTML con JQuery porque me pareció mas prolijo que poner texto todo escapado.
                <?php $i = 1;  
                foreach ($exchange['Exchange']['photos'] as $photo) { ?>
                    {
                        content: $('<div>').append(
                            $('<div>').attr('class', 'slide_inner').append(
                                $('<a>').attr('class','photo_link').attr('href','javascript:viewPhoto("<?php echo $photo['small']['url'] ?>", "<?php echo "carouselImage$i" ?>");').append(
                                    $('<img>').attr('src', '<?php echo $photo['small']['url'] ?>').attr('class','photo').attr('id', '<?php echo "carouselImage$i" ?>')
                            )).append(
                                $('<a>').attr('href', '#').attr('class', 'caption').text('descripcion')
                            )
                        ).html(),
                        content_button: $('<div>').append(
                            $('<div>').attr('class', 'thumb').append(
                                $('<img>').attr('src', '<?php echo $photo['square']['url'] ?>').attr('alt', 'thumb'))
                            .append(
                                $('<p>').text('Foto <?php echo $i ?>')
                            )
                        ).html()
                    },
                <?php $i++; } ?>
            ];
             
            $("#exchange-photos").agile_carousel({
                carousel_data: data,
                carousel_outer_height: 330,
                carousel_height: 230,
                slide_height: 230,
                carousel_outer_width: 480,
                slide_width: 480,
                transition_type: "fade",
                transition_time: 600,
                timer: 3000,
                continuous_scrolling: true,
                control_set_1: "numbered_buttons,previous_button,pause_button,next_button",
                control_set_2: "content_buttons",
                change_on_hover: "content_buttons"
            });
        });
    </script>
    
	<div id="exchange-photos" class="exchange-photos" ></div>
    <div class="clear"></div>
    <?php } ?>

	<ul class="exchange-comment-list">
		<?php
		if (isset($exchange['Exchange']['comments'])) { 
			foreach($exchange['Exchange']['comments'] as $i => $comment) { ?>
			<li class="exchange_comment">
				<div style="float:left">
					<h4 class="exchange_comment_header"><?php echo $comment['username']." (".$this->Time->timeAgoInWords($comment['created'],true).")"?></h4>
					<p class="exchange_comment_text"><?php echo $comment['text'];?></p>
				</div>
				<div class="exchange_comment_user_info">
					<?php
                        if($current_user && $current_user['User']['admin']){
                            $icon =  $this->Html->image('/img/icons/erase.png',array('style'=>"width: 14px; height: 14px; margin-right: 5px; margin-bottom: 7px;"));
                            echo $this->Html->link($icon,array('action'=>'remove_comment',$comment['user_id'],$i),array('escape'=>false));
                        }
                        echo $this->Html->link($this->Html->image('/img/icons/mail.png'),'/conversations/add/'.$comment['user_id'],array('escape'=>false,'title'=>"Enviar mensaje personal"));?>
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
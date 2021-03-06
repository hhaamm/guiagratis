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
<?php if ($share_exchange_on_facebook == true) { ?>
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '<?php echo Configure::read('Facebook.app_id'); ?>', // App ID
	//TODO: cambiar esto
      channelUrl : '//www.guia-gratis.com.ar/channel.html', // Channel File
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });

    // Additional initialization code here
    shareExchange();
  };

  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));

function shareExchange() {
	FB.ui({
	      method: 'feed',
	      name: '<?php echo $exchange['Exchange']['title']; ?>',
	      description: '<?php echo strip_tags($exchange['Exchange']['detail']); ?>',
	      //picture: 'http://fbrell.com/f8.jpg',
	      link: '<?php echo Configure::read('Host.url') ?>/exchanges/views/<?php echo $exchange['Exchange']['id']; ?>'
	      },    
		function (response) {
			if (response && response.post_id) {

			} else {

			}    
		}  
	);
}    
</script>
<?php } ?>
<?php
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
    <?php
        $avatar_url = '/img/default_avatar.png';
        if(isset($owner['User']['avatar'])){
            $avatar_url = $this->Exchange->imageUrl($owner['User']['avatar'], 'medium_square');
        }
        $avatar =  $this->Html->image($avatar_url,array('style'=>"width: 50px; height: 50px;"));
        echo $this->Html->link($avatar,array('controller'=>'users','action'=>'view',$owner['User']['id']),array('escape'=>false,"style"=>"float:left;50px; margin-right: 5px;"))
    ?>
    <div class="exchange-type <?php echo $this->Exchange->cssClass($exchange); ?>">
    <?php echo $this->Exchange->type($exchange); ?>
    </div>
	<h2><?php echo $exchange['Exchange']['title']?></h2>
        <p>Creado por <?php echo $this->Html->link($owner['User']['username'] ,'/users/view/'.$owner['User']['id'], array('style'=> 'text-decoration: none;' ));  ?> <?php echo $this->Time->timeAgoInWords($exchange['Exchange']['created']) ;?> </p>
	<p></p>
    <!-- SHOW ONLY WHEN IS OWNER -->
    <?php if( !empty($current_user) && ($current_user['User']['id'] == $owner['User']['id'] || $current_user['User']['admin']) ){ ?>
	<div class="admin edit-exchange-menu" style="float: right;">
    <?php
        if ($exchange['Exchange']['state'] != EXCHANGE_FINALIZED) {
            $icon =  $this->Html->image('/img/icons/modify.png');
            echo $this->Html->link($icon.' Editar',array('controller'=>'exchanges','action'=>'edit',$exchange['Exchange']['id']),array('class'=>"link-button", 'escape' => false));

            $icon =  $this->Html->image('/img/icons/photo.png');
            echo $this->Html->link($icon.' Editar fotos',array('controller'=>'exchanges','action'=>'edit_photos',$exchange['Exchange']['id']),array('class'=>"link-button", 'escape' => false));

            $icon =  $this->Html->image('/img/icons/terminate.png');
            echo $this->Html->link($icon.' Finalizar','/exchanges/finalize/'.$exchange['Exchange']['id'], array('class'=>"link-button", 'escape' => false) , "Una vez que finalizes el intercambio dejará de estar publicado. ¿Estás seguro?");
        }else{
            echo $this->Html->div('link-button',
                $this->Html->image('/img/icons/abort.png').
                " Finalizado",
                array('style'=>'background-color:#DDDDDD'));
        }
        if($current_user['User']['admin']){
            $icon =  $this->Html->image('/img/icons/erase.png');
            echo $this->Html->link($icon.' Borrar',array('controller'=>'exchanges','action'=>'delete',$exchange['Exchange']['id']),array('class'=>"link-button", 'escape' => false),"Esta acción es irreversible. Usar solo para contenido basura(CRAP) \\n ¿Seguro que deseas continuar?");
        }
	?>
	</div>

    <?php }else{
        if ($exchange['Exchange']['state'] == EXCHANGE_FINALIZED) {
            echo $this->Html->div('link-button',
                $this->Html->image('/img/icons/abort.png').
                " Finalizado ".$this->Time->timeAgoInWords($exchange['Exchange']['finalize_time']),
                array('style'=>'background-color:#DDDDDD;float:right'));
        }
    }?>

    <?php
    if ($this->Exchange->is_service($exchange)) {
        echo $this->Html->div('hours_of_opening', 'Horario de atención: '.$exchange['Exchange']['hours_of_opening']);
    }
    if ($this->Exchange->is_event($exchange)) {
        echo $this->Html->div('service_start_date', 'Empieza: '.date('Y-m-d H:i', $exchange['Exchange']['start_date']->sec));
        echo $this->Html->div('service_end_date', 'Termina: '.date('Y-m-d H:i', $exchange['Exchange']['end_date']->sec));
    }
    ?>

	<br/>

    <div class="clear"></div>
    <?php echo $this->element("minimap",array('exchange'=>$exchange))?>
    
    <div class="exchange-description" style="margin-left:200px;">
	    <?php echo $exchange['Exchange']['detail'] ?>
    </div>

	    <p class="exchange-location" style="margin-left:200px;margin-top:5px;">Ubicación: <strong><?= $this->Exchange->ubicacion($exchange); ?></strong></p>
    <p class="exchange-comment-tags" style="margin-left:200px;margin-top:5px;" >
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

    <div style="margin-left: 200px; ">
       <?php echo $this->element('rating_bar');?>
       <script type="text/javascript">

          $("#thumb-up").bind('click',function(){
              rate("positive","thumb_up");
            return false;
          })
          $("#thumb-down").bind('click',function(){
            rate("negative","thumb_down");
            return false;
          })
          changeRatingBar(<?php echo $rates['positives']?>,<?php echo $rates['negatives']?>);

          function rate(valoration,icon){
            eid = "<?php echo $exchange['Exchange']['id']?>";
            toggleLoader(icon);
            $.ajax({
                type: "GET",
                url:"/exchanges/rate/"+valoration+"/"+eid,
                success: function(response){
                    toggleLoader(icon);
                    if(response.result){
                        changeRatingBar(response.data.positives,response.data.negatives);
                    }else{
                        alert(response.message)
                    }
                }
            });
          }

       </script>
    </div>
    <table style="margin: 5px; float: right;" cellspacing="5">
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

    <div class="clear"> </div>

    <?php if(!empty($exchange['Photo'])){ ?>
       <!-- Solo para testear -->
     <script type="text/javascript">
         $(document).ready(function(){
            var data = [
                // Creamos un div exterior para poder extraerle el HTML. Por eso hay dos divs, pero el resultado devuelve sólo uno!
                // Generé el HTML con JQuery porque me pareció mas prolijo que poner texto todo escapado.
                <?php $i = 1;  
                foreach ($exchange['Photo'] as $photo) { ?>
                    {
                        content: $('<div>').append(
                            $('<div>').attr('class', 'slide_inner').append(
                                $('<a>').attr('class','photo_link').attr('href','javascript:viewPhoto("<?php echo $this->Exchange->imageUrl($photo['file_name'], 'small'); ?>", "<?php echo "carouselImage$i" ?>");').append(
                                    $('<img>').attr('src', '<?php echo $this->Exchange->imageUrl($photo['file_name'], 'small') ?>').attr('class','photo').attr('id', '<?php echo "carouselImage$i" ?>')
                            )).append(
                                $('<a>').attr('href', '#').attr('class', 'caption').text('descripcion')
                            )
                        ).html(),
                        content_button: $('<div>').append(
                            $('<div>').attr('class', 'thumb').append(
                                $('<img>').attr('src', '<?php echo $this->Exchange->imageUrl($photo['file_name'], 'square'); ?>').attr('alt', 'thumb'))
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

    <br/>
    <br/>

	<ul class="exchange-comment-list">
		<?php
		if (isset($exchange['Comment'])) { 
			foreach($exchange['Comment'] as $i => $comment) { ?>
			<li class="exchange_comment">
                <div style="float:left">
                    <?php
                        $avatar_url = '/img/default_avatar.png';
                        if(isset($comment['User']['avatar'])){
                            $avatar_url = $this->Exchange->imageUrl($comment['User']['avatar'], 'square');
                        }
                        $avatar =  $this->Html->image($avatar_url, array('style'=>"width: 50px; height: 50px;"));
                        echo $this->Html->link($avatar,array('controller'=>'users','action'=>'view',$comment['user_id']),array('escape'=>false))
                    ?>
                </div>
				<div style="float:left;margin-left: 10px;">
                    <?php echo $this->Html->link($comment['User']['username'],array('controller'=>'users','action'=>'view',$comment['user_id']),array('style'=>'text-decoration:none')) ?>
					<span class="exchange_comment_header">
                        <?php echo  " (".$this->Time->timeAgoInWords($comment['created'],true).")"?>
                    </span>
					<p class="exchange_comment_text" style="width: 690px;"><?php echo $comment['detail'];?></p>
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
    
	
	<fieldset>
		<legend><?php
                switch($exchange['Exchange']['exchange_type_id']) {
                    case EXCHANGE_OFFER:
                        echo "¿Necesitás este artículo?";
                        break;
                    case EXCHANGE_REQUEST:
                        echo "¿Querés donar este artículo?";
                        break;
                    case EXCHANGE_SERVICE:
                        echo "¿Querés preguntar sobre este servicio?";
                        break;
                    case EXCHANGE_EVENT:
                        echo "¿Querés preguntar sobre este evento?";
                        break;
                }
                
                ?></legend>

<a name="comment"></a>
		<?php
		if ($current_user) {

			echo $this->Form->create('ExchangeComment',array('url'=>'/exchanges/add_comment'));
			echo $this->Form->input('detail',array('type'=>'textarea','label'=>'Contanos'));
			echo $this->Form->hidden('exchange_id', array('default'=>$exchange['Exchange']['id']));
			echo $this->Form->end('Comentá');
		} else {
			echo "Tenés que ".$this->Html->link('loguearte','/users/login')." para poder responder. ";
			echo "Hacé click ".$this->Html->link('acá','/users/register')." para registrarte.";
		}

		?>
	</fieldset>

	<a href="/exchanges/own">Volver</a>
</div>
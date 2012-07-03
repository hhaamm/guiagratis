<div id="exchanges-list-page">
<?php
//TODO pasar los estilos al css
$types =   Configure::read('ExchangeType.Names');
$types[EXCHANGE_ALL] = "Todos";
?>

<!--
Busqueda: <br/>


<form id="search-form" method="GET" action="<?php echo $this->Html->url($this->here);?>">
    <div >

         <div style="float: left;float: left; padding-top: 0px;">
            <label for="type" >Tipo</label>
            <select id="type" name="type" style=" width: 88px; height: 40px; text-align: center; padding-top: 7px;">
                <?
                 foreach($types as $tid => $label){
                    $options= array('value'=>$tid,);
                    if(isSet($_GET['type']) && $tid == $_GET['type']){
                        $options['selected'] = "selected";
                    }
                    echo $this->Html->tag('option',$label,$options);
                 }
                ?>
            </select>
        </div>



         <label for="mode-tags" style="float: left;margin-bottom: 0px;"><input id="mode-tags" type="radio" name="mode" value="0" <?php echo $mode!=1 ? 'checked="checked"' :'' ?> style="width: 14px;" /> Tags separados por comas</label>
         <label for="mode-title" style="float: left; margin-bottom: 0px; margin-left: 10px;;"><input id="mode-title" type="radio"  <?php echo $mode!=0 ? 'checked="checked"' :'' ?> name="mode" value="1" style="width: 14px;" />  Titulo</label>


        <input id="query" name="query" value="<?php echo isSet($_GET['query']) ? $_GET['query'] : ''?>" style="width: 510px;"/>
        <a href="#" class="link-button" onclick="$('#search-form').submit();return false;" style="float: right; height: 30px; margin-top: 0px;">
            <?php echo $this->Html->image('icons/search.png')?>
            Buscar
        </a>
        <div class="clear"></div>
    </div>

</form>
-->

<ul id="exchanges">
<?php foreach ($exchanges as $e) { ?>
        <li>
	<h3><?php echo $this->Html->link($e['Exchange']['title'], '/exchanges/view/'.$e['Exchange']['_id']); ?></h3>
	 <?php 
	 if ($this->Exchange->defaultPhotoUrl($e)) {
		 echo $this->Exchange->defaultPhoto($e); 
	 }
         ?>
<p class="datos-adicionales"><span class="tipo ">Oferta</span> Publicado <?php echo $this->Time->timeAgoInWords($e['Exchange']['created'])?> por <a href="#">pachamama</a></p>
	 <?php echo $e['Exchange']['detail']; ?>
<div class="links">
<?php echo $this->Html->link('+enviar mensaje', ''); ?>
<?php echo $this->Html->link('+comentar', ''); ?>
</div>
	</li>
 <?php } ?>
</ul>
<div class="clear"></div>


<?php
if(isSet($_GET['query'])){
 if(!empty($exchanges))
    foreach($exchanges as $e) { ?>
        <div  style="margin: 10px 0px;">
            <?php echo   $this->Html->link($this->Exchange->defaultPhoto($e),array('action'=>'view','controller'=>'exchanges',$e['Exchange']['_id']),array('escape'=>false , 'style' => 'float:left;margin-right: 10px;')) ?>
            <div style="float: left;">
             <p><?php echo Configure::read('ExchangeType.Names.'.$e['Exchange']['exchange_type_id']); ?>  - publicado  <?php echo $this->Time->timeAgoInWords($e['Exchange']['created'])?></p>
             <?php echo $this->Html->link($e['Exchange']['title'],array('action'=>'view','controller'=>'exchanges',$e['Exchange']['_id']),array('class'=>'exchange-view-link'))?>
            </div>
            <div class="clear"></div>
        </div>
<?php
    }else{
?>
      <div style="text-align: center;">
       <h2>La busqueda no produjo ningun resultado</h2>
      </div>
<?php
    }
  }
?>
</div>
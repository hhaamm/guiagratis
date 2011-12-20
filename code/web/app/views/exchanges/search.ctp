Busqueda: <br/>


<form id="search-form" method="GET" action="<?php echo $this->Html->url($this->here);?>">
    <div >

         <div style="float: left;float: left; padding-top: 0px;">
            <label for="type" >Tipo</label>
            <select id="type" name="type" style=" width: 88px; height: 40px; text-align: center; padding-top: 7px;">
                <option value="<?php echo Configure::read('ExchangeType.Request')?>">Pedidos</option>
                <option value="<?php echo Configure::read('ExchangeType.Offer')?>">Ofertas</option>
                <option value="<?php echo Configure::read('ExchangeType.All')?>" selected="selected">Todos</option>
            </select>
        </div>

        <label for="tags" >Tags separados por comas</label>
        <input id="tags" name="tags" value="<?php echo $_GET['tags']?>" style="width: 510px;"/>
        <a href="#" class="link-button" onclick="return false;" style="float: right; height: 30px; margin-top: 0px;">
            <?php echo $this->Html->image('icons/search.png')?>
            Buscar
        </a>
        <div class="clear"></div>
    </div>

</form>


<?php
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
?>

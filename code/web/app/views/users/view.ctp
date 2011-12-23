
<p>
    Perfil de usuario:  <?php echo $user['User']['username'] ?> (<?php echo $this->Html->link('PM','/conversations/add/'.$user['User']['_id']);?>)
</p>

<br/>
<br/>
<br/>


  <?php
    if(isset($user['User']['firstname'])){
         echo $user['User']['firstname'];
    }
    ?>

<br/>
<br/>
<br/>

<h2> Pedidos y ofertas de este usuario </h2>

<?php
  if(!empty($exchanges)){
    foreach($exchanges as $e) { ?>
        <div  style="margin: 10px 0px;">
            <?php echo   $this->Html->link($this->Exchange->defaultPhoto($e),array('action'=>'view','controller'=>'exchanges',$e['Exchange']['_id']),array('escape'=>false , 'style' => 'float:left;margin-right: 10px;')) ?>
            <div style="float: left;">
             <p><?php echo Configure::read('ExchangeType.Names.'.$e['Exchange']['exchange_type_id']); ?>  - publicado  <?php echo $this->Time->timeAgoInWords($e['Exchange']['created'])?></p>
             <?php echo $this->Html->link($e['Exchange']['title'],array('action'=>'view','controller'=>'exchanges',$e['Exchange']['_id']),array('class'=>'exchange-view-link'))?>
            </div>
            <div class="clear"></div>
        </div>
<?php }
  }else{
    echo $this->Html->tag('h1','Este usuario no ha publicado nada aun');
  }
?>

<br/>
<br/>
    
<?php #Debugger::dump($user['User']); ?>
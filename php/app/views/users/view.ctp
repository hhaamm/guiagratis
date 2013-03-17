
<div style="">
<?php
  if(isset($current_user) && $user['User']['id']==$current_user['User']['id']){
    $icon =  $this->Html->image('/img/icons/modify.png');
    echo $this->Html->div(null,
       $this->Html->link($icon.' Editar perfil',array('controller'=>'users','action'=>'edit_profile',),array('class'=>"link-button", 'escape' => false)),
       array('style'=> 'float: right;')
    );
  }
?>
</div>
    
<h3> Perfil de usuario </h3>

<br/>


 <div style="float: left;margin-left: 30px;">
     <?php
     $avatar_url = '/img/default_avatar.png';
     if(isset($user['User']['avatar']) && !empty($user['User']['avatar']) ){
      $avatar_url =  $user['User']['avatar']['large']['url'];
     }
     echo $this->Html->image($avatar_url) ?> <br/>
     <?php echo $user['User']['username'] ?>
 </div>

 <div style="float: left;margin-left: 70px;">
   <h2> Datos personales </h2>
    
  <?php
    $shared = false;
    $personal_data = array('Nombre'=>'firstname', 'Apellido'=>'lastname','Localidad'=>'city','Región'=> 'region' , 'Pais'=>'country');
    foreach($personal_data as $label => $value){
        if(isset($user['User'][$value]) && !empty($user['User'][$value])){
            $shared = true;
            echo $this->Html->tag('b',$label).": ".$user['User'][$value];
            echo "<br/>";
        }
    }
    if(!$shared){
      echo "<p>El usuario no ha compartido<br/>  datos personales</p>";
    }
  ?>
 </div>

<div style="float: left; margin-left: 70px;">
 <h2> Contacto </h2>
    <?php
     if(isset($current_user)){
          echo $this->Html->image('/img/icons/mail.png',array('style'=>'margin-bottom: -8px;'));
          echo $this->Html->link(" Enviar mensaje privado",'/conversations/add/'.$user['User']['id'],array('style'=>'text-decoration:none;','title'=>"Enviar mensaje personal"));
          echo "<br/>";
          if(isset($user['User']['telephone']) && !empty($user['User']['telephone'])){
                  echo $this->Html->tag('b',"Telefono").": ".$user['User']['telephone'];
          }
          echo "<br/>";
          if(isset($user['User']['show_email']) && !empty($user['User']['show_email'])){
                  echo $this->Html->tag('b',"Email").": ".$user['User']['email'];
          }
      }else{
        echo "<p>".$this->Html->link(" Logueate",'/users/login/'). " o ". $this->Html->link("Registrate",'/users/register/')." para <br/> contactar a este usuario</p>" ;
     }
    ?>
</div>

<br/><br/>
<div class="clear"></div>

<?php
 if(isset($user['User']['description']) && !empty($user['User']['description'])){
?>
 <div style="margin-top: 26px;" >
  <h2> Sobre <?php echo $user['User']['username'] ?> </h2>
  <p><?php echo $user['User']['description'] ?></p>
 </div>
<?php
}
?>


<br/>
<br/>

<h2> Pedidos y ofertas de este usuario </h2>

<?php
  if(!empty($exchanges)){
    foreach($exchanges as $e) { ?>
        <div  style="margin: 10px 0px;">
            <?php echo   $this->Html->link($this->Exchange->defaultPhoto($e),array('action'=>'view','controller'=>'exchanges',$e['Exchange']['id']),array('escape'=>false , 'style' => 'float:left;margin-right: 10px;')) ?>
            <div style="float: left;">
             <p><?php echo Configure::read('ExchangeType.Names.'.$e['Exchange']['exchange_type_id']); ?>  - publicado  <?php echo $this->Time->timeAgoInWords($e['Exchange']['created'])?></p>
             <?php echo $this->Html->link($e['Exchange']['title'],array('action'=>'view','controller'=>'exchanges',$e['Exchange']['id']),array('class'=>'exchange-view-link'))?>
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
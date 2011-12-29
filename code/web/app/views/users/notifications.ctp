<h2>Notificaciones</h2>

<?php
   $notification_counter = 0;
   if(isSet($notifications)){
       foreach($notifications as $notification){
           $notification_counter += $notification['has_been_read'] ? 0 : 1 ;
       }
   }
  if($notification_counter == 0){
   $message =  "No hay notificaciones nuevas";
  }else if($notification_counter == 1){
   $message = "Tienes una notificación nueva";
  }else{
   $message = "Tienes $notification_counter notificaciones nuevas";
  }
  echo $this->Html->tag('h1',$message);
?>


<br/>

<ul>
    <?php
     $new_icon = $this->Html->image('/img/icons/new.png',array('style'=>'margin: 2px 2px -7px;'));
     //DCDCDC
     if(isSet($notifications)){
       foreach($notifications as $notification){
           $links = array();
             if(isSet($notification['links'])){
              foreach($notification['links'] as $text => $url){
                $links[] = $this->Html->link($text,$url);
              }
             }
             $class = $icon = (! $notification['has_been_read'] ? "notification-item-unread" : "notification-item");
             $icon = (! $notification['has_been_read'] ? $new_icon." " : "");
             echo $this->Html->tag('li',
              $icon." ".$this->Html->tag('span',vsprintf($notification['description'],$links)),
              array('class'=>"$class"));
       }
     }
    ?>
</ul>

<br/>
<?php
$latitude = $exchange['Exchange']['lat'];
$longitude = $exchange['Exchange']['lng'];

switch($exchange['Exchange']['exchange_type_id']) {
    case EXCHANGE_OFFER:
         $label = "O";
         $color = "0xFF9305";
        break;
    case EXCHANGE_REQUEST:
         $label = "P";
         $color = "0xCD05FF";
        break;
    case EXCHANGE_SERVICE:
         $label = "S";
         $color = "0x5658F5";
        break;
    case EXCHANGE_EVENT:
         $label = "E";
         $color = "0xEEEEEE";
        break;
}


echo   $this->Html->image("http://maps.google.com/maps/api/staticmap?center=$latitude,$longitude&zoom=7&size=186x186&maptype=roadmap&markers=color:$color|label:$label|$latitude,$longitude&sensor=false",
                           array('style'=>'float:left;'))
?>

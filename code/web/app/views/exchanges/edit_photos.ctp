<?php

$javascript->link('jquery',false);
$javascript->link('ajaxupload',false);
echo $this->element('photo_uploader',array('prefix'=>'exchange_photo','eid'=>$exchange_id,'e'=>$e));
echo $this->Html->link('Volver','/exchanges/view/'.$exchange_id);
?>
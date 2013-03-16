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
<?php
    $javascript->link('jquery',false);
    $javascript->link('ajaxupload',false);
?>

<div style="float: left;margin-left: 130px;">
    <?php
      $avatar_url = '/img/default_avatar.png';
      if(isset($current_user['User']['avatar']['medium']['url']) && !empty($current_user['User']['avatar']['medium']['url']) ){
        $avatar_url = $current_user['User']['avatar']['medium']['url'];
      }
      echo $this->Html->image($avatar_url,array('style'=>"width: 100px; height: 100px;","id"=>"avatar"));
    ?>
</div>
<div style="float: left;margin-top: 15px;">
  <?php  echo $this->element('image_uploader',array('prefix'=>'user_avatar','id'=>$current_user['User']['id'],'url'=>'/users/change_avatar','image_id'=>"avatar")) ?>
</div>

<?php

    echo $this->Form->create('User',array('action'=>'edit_profile'));
    echo $this->Form->input('firstname',array('type'=>'text','label'=>'Nombre'));
    echo $this->Form->input('lastname',array('type'=>'text','label'=>'Apellido'));
    echo $this->Form->input('telephone',array('type'=>'text','label'=>'Teléfono (Solo lo veran quienes esten registrados)'));
    // $this->Form->input('birthday',array('type'=>'text','label'=>'Fecha de nacimiento','dateFormat'=>'DMY'));
    echo $this->Form->input('city',array('type'=>'text','label'=>'Localidad'));
    echo $this->Html->tag('span','Region / Provincia',array('style'=>'margin-left: 10px;font-size: 110%;color: #444444;','for'=>'UserRegion')).
         $this->Html->tag('span','Pais',array('style'=>'margin-left: 300px;font-size: 110%;color: #444444;','for'=>'UserCountry'));

    echo $this->Html->div("input text",
         $this->Form->input('region',array('type'=>'text','style'=>'width: 390px;','label'=>'Región','div' => false,'label' => false )).
         $this->Form->input('country',array('type'=>'text','style'=>'width: 300px; margin-left: 30px;','label'=>'Pais','div' => false,'label' => false ))
    );
    echo $this->Form->input('description',array('type'=>'textarea','label'=>'Cuentanos sobre ti (esto te ayudara a recibir regalos)'));
    echo $this->Form->input('show_email',array('type'=>'checkbox','label'=>'Hacer público mi email (Solo lo veran quienes esten registrados)'));

    echo $this->Form->end('Guardar');

?>

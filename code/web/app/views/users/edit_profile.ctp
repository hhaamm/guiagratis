<?php

    echo $this->Form->create('User',array('action'=>'edit_profile'));
    echo $this->Form->input('firstname',array('type'=>'text','label'=>'Nombre'));
    echo $this->Form->input('lastname',array('type'=>'text','label'=>'Apellido'));
    echo $this->Form->input('telephone',array('type'=>'text','label'=>'Teléfono (Solo lo veran quienes esten registrados)'));
    // $this->Form->input('birthday',array('type'=>'text','label'=>'Fecha de nacimiento','dateFormat'=>'DMY'));
    echo $this->Form->input('city',array('type'=>'text','label'=>'Localidad'));
    echo $this->Html->tag('span','Region / Provincia',array('style'=>'margin-left: 10px;font-size: 110%;color: #444444;','for'=>'UserRegion')).
         $this->Html->tag('span','Pais',array('style'=>'margin-left: 375px;font-size: 110%;color: #444444;','for'=>'UserCountry'));

    echo $this->Html->div("input text",
         $this->Form->input('region',array('type'=>'text','style'=>'width: 390px;','label'=>'Región','div' => false,'label' => false )).
         $this->Form->input('country',array('type'=>'text','style'=>'width: 300px; margin-left: 30px;','label'=>'Pais','div' => false,'label' => false ))
    );
    echo $this->Form->input('description',array('type'=>'textarea','label'=>'Cuentanos sobre ti (esto te ayudara a recibir regalos)'));
    echo $this->Form->input('show_email',array('type'=>'checkbox','label'=>'Hacer público mi email (Solo lo veran quienes esten registrados)'));

    echo $this->Form->end('Guardar');

?>

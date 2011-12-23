<?php

    echo $this->Form->create('User',array('action'=>'edit_profile'));
    echo $this->Form->input('firstname',array('type'=>'text','label'=>'Nombre'));
    echo $this->Form->input('lastname',array('type'=>'text','label'=>'Apellido'));
    echo $this->Form->input('city',array('type'=>'text','label'=>'Ciudad'));
    echo $this->Html->div(null,
         $this->Form->input('region',array('type'=>'text','style'=>'width: 390px;','label'=>'Región')).
         $this->Form->input('country',array('type'=>'text','style'=>'width: 250px;','label'=>'Pais'))
    );
    echo $this->Form->input('description',array('type'=>'textarea','label'=>'Cuentanos sobre ti (esto te ayudara a recibir regalos)'));

    echo $this->Form->end('Guardar');

?>

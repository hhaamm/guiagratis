<fieldset>
	<legend>Registro de usuario</legend>
<?php

echo $this->Form->create('User');
echo $this->Form->input('username',array('label'=>'Nombre de usuario'));
echo $this->Form->input('mail',array('label'=>'Email'));
echo $this->Form->input('password',array('label'=>'Contraseña'));
echo $this->Form->input('confirm_password', array('type'=>'password','label'=>'Confirmar contraseña'));
echo $this->Form->input('terms',array('type'=>'checkbox','label'=>"Acepto haber leído y entendido los <a href='/pages/tys' target='_blank'>términos y condiciones</a>"));
echo $this->Form->end('¡Registrame!');
?>
</fieldset>
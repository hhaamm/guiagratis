<fieldset>
	<legend>Login</legend>
	<?php
	echo $form->create('User', array('action' => 'login', 'id' => 'login_user'));
	?>
	<div id="login-message"></div>
	<?php
	echo $form->input('mail',array('label'=>'Email'));
	echo $form->input('password',array('label'=>'Contraseña'));
	echo $form->end('Ingresar');
	?>
	<div class="clear"/>
	<div class="floatright"><a href="/users/forgot_password"><?__('Olvidé mi contraseña')?></a></div>
</fieldset>
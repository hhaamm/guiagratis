<? __('Ingresá el código que te mandamos a tu mail y la nueva contraseña.')?>

<?
echo $form->create('User');
echo $form->input('mail',array('label'=>'Email'));
echo $form->input('reset_password_code',array('label'=>'Código'));
echo $form->input('password',array('label'=>'Nueva contraseña'));
echo $form->input('confirm_password', array('type' => 'password','label'=>'Confirmar nueva contraseña'));
echo $form->end(__('Enviar',true));
?>
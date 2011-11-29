<? __('Ingresá tu mail y te enviaremos un código para reestablecer tu contraseña.')?>

<?
echo $form->create('User');
echo $form->input('mail');
echo $form->end(__('Enviar',true));
?>
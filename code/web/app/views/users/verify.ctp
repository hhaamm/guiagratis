<? if ($action == 'cellphone') { ?>
<? __('Verify your account! enter your cellphopne')?>
<?php
echo $form->create(null, array('class' => 'delivery-form'));
echo $form->input('cellphone', array('name' => 'cellphone'));
echo $form->end(__('Verify',true));
} else {
?>
<? echo sprintf(__("A token was just sent to %s . Please enter your code here and your account will be verified.", true), $cellphone); ?>
<?php
echo $form->create(null, array('class' => 'delivery-form', 'action' => 'verify_token'))    ;
echo $form->input('verify_token', array('name' => 'verify_token'));
echo $form->end(__('Verify',true));
}
?>
<?
echo $this->element('setting', array('id' => 'pic_form', 'title' => __('Picture',true), 'desc' => __("Change your avatar",true)));
echo $this->element('image_upload_2', array('fieldPic' => 'pic', 'modelName' => 'User', 'data' => $user));
echo $this->element('setting', array('footer' => true));

echo $this->element('setting', array('id' => 'settings-nickname', 'title' => __('Nickname',true), 'desc' => __('Short access to your refrigerator from everywhere',true), 'value'=>$user['Nickname']['detail']));
echo $form->create('User', array('url' => '/nicknames/change'));
echo $form->input('Nickname.detail', array('id' => 'nickname', 'label' => __('New nickname',true), 'default' => @$user['Nickname']['detail']));
echo $form->input('User.public_profile', array('checked' => !!$user['User']['public_profile'], 'type' => 'checkbox'));
echo $form->submit(__('Change nickname',true));
echo $form->end();
echo $this->element('setting', array('footer' => true));

echo $this->element('setting', array('id' => 'settings-password', 'title' => __('Password',true), 'desc' => __('What you use to log in',true), 'value'=>'*****'));
echo $form->create('User', array('action' => 'change_password'));
echo $form->input('old_password', array('type' => 'password','label'=>__('Old password',true)));
echo $form->input('new_password', array('type' => 'password','label'=>__('New password',true)));
echo $form->input('confirm_password', array('type' => 'password','label'=>__('Confirm password',true)));
echo $form->submit(__('Change password',true));
echo $form->end();
echo $this->element('setting', array('footer' => true));

echo $this->element('setting', array('id' => 'contributions', 'title' => __('Contributions',true), 'desc' => __("See all your contributions",true)));
echo $html->link(__('See my contributions',true), '/users/contributions/'.$user['User']['id']);
echo $this->element('setting', array('footer' => true));

echo $this->element('setting', array('id' => 'role', 'title' => __('Account level',true), 'desc' => __("Your current user level",true), 'value' => $user['Role']['name']));
__("Current level");
echo "<br>";
echo $user['Role']['name'];
if ($user['Role']['name'] == 'user') {
    echo "<br>";
    echo "<br>";
    echo $html->link(__("I want to be a moderator and contribute for this wiki",true), "/moderatorship_requests/add");
}
if ($user['Role']['name'] == 'moderator') {
    echo "<br><br>";
    echo $html->link(__("Moderator dashboard",true), "/moderator/dashboard");
}
echo $this->element('setting', array('footer' => true));


echo $this->element('setting', array('id' => 'settings-delete-account', 'title' => __('Delete account',true)));
 __('Are you shure you want to delete your account?');
 echo "<br>";
__('Once an account is deleted, you will not be able to get your data back.');
echo "<br>";
echo $form->create('User', array('action' => 'delete'));
echo $form->input('password', array('type' => 'password'));
echo $form->submit( __('Delete my account',true));
echo $form->end();
echo $this->element('setting', array('footer' => true));
?>
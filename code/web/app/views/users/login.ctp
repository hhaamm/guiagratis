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
 */
?>
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '<?php echo Configure::read('Facebook.app_id'); ?>', // App ID
	//TODO: cambiar esto
      channelUrl : '//www.guia-gratis.com.ar/channel.html', // Channel File
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });

    // Additional initialization code here
  };

  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));
</script>
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
<fieldset>
	<legend>Facebook login</legend>
	<a href="#" onclick="fbLogin(); return false;">Loguearse con Facebook</a>
</fieldset>

<script type="text/javascript">
	function fbLogin() {
	  FB.login(function(response) {
	      if (response.authResponse) {
	      	window.location = '/users/facebook_login';
	      } else {
		alert("No se pudo loguear con Facebook. Intente nuevamente.");
	      }
	    }, {scope:'email'});
	}
</script>
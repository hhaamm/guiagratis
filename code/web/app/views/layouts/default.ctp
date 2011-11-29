<html>
	<head>
		<?php echo $this->Html->charset(); ?>
		<title>¿Necesitás algo? Conseguilo en guia-gratis.com.ar</title>
		<?php
		echo $javascript->link('debug');
		echo $javascript->link('jquery');
		echo $javascript->link('common');
		echo $javascript->link('jquery-ui-1.8.2.custom.min');
		echo $javascript->link('sexy_dropdown_menu');

		echo $scripts_for_layout;

		echo $this->Html->css('default.css');
		echo $this->Html->css('TextboxList.Autocomplete.css');
		echo $this->Html->css('TextboxList.css');
		echo $this->Html->css('jquery-ui');
		echo $this->Html->css('sexy_dropdown_menu');

		echo $this->Html->meta('icon', '/favicon2.ico');
		?>
	</head>
	<body>
		<div id="content">
			<ul class="topnav top-menu">
				<li><a href="/">Home</a></li>
				<?php if ($current_user) { ?>
				<li>
					<a href="#">Acciones</a>
					<ul class="subnav">
						<li><a href="/exchanges/add_offer">Agregar oferta</a></li>
						<li><a href="/exchanges/add_request">Agregar pedido</a></li>
					</ul>
				</li>
				<li>
					<a href="#">Cuenta</a>
					<ul class="subnav">
						<li><a href="/conversations">Mis conversaciones</a></li>
						<li><a href="/exchanges/own">Mis ofertas/pedidos</a></li>
						<li><a href="/users/logout">Salir</a></li>
					</ul>
				</li>
					<?php } else { ?>
				<li><a href="/users/login">Entrar</a></li>
				<li><a href="/users/register">Registrarse</a></li>
					<?php } ?>
				<li><a href="/pages/about_us">Quiénes somos</a></li>
				<li><a href="/pages/help">Ayuda</a></li>
			</ul>
			<div class="br"></div>
			<div class="body">
				<?php echo $this->Session->flash(); ?>
				<?php echo $content_for_layout; ?>
			</div>
			<?php echo $this->element('sql_dump'); ?>
			<div class="footer"></div>
		</div>
	</body>
</html>
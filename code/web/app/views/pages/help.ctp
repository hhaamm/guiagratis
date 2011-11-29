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
<h2>Ayuda</h2>
<p>
	Esta página explica las acciones básicas que se pueden realizar en <b>GuiaGratis</b>. Si querés
	leer sobre el objetivo de la página y el concepto detrás de ella, hacé click <a href="/pages/about_us">acá</a>.
</p>
<br/>
<h3>Registrándose</h3>
<p>
	Si no estás registrado lo único que vas a poder hacer va a ser mirar las ofertas y pedidos disponibles.
	Estando registrado, podés agregar pedidos (cosas que quieras recibir), agregar ofertas (cosas que quieras regalar)
	y mandar mensajes privados a los usuarios.

	Para registrarte, tenés que hacer click en "Registrarse" en la barra de arriba y llenar el formulario.
	Un mail será enviado a tu casilla de correo para confirmar que sos dueño de esa dirección de mail
	y terminar de crear tu cuenta. ¡Ojo! <b>Si no recibís el mail es posible que haya caído en la carpeta SPAM
	de tu cliente de correo</b>.
</p>
<br/>
<h3>Login</h3>
<p>
	Una vez que creaste la cuenta, tenés que identificarte ("loguearte") para poder agregar ofertas, pedidos, etc.
	Para esto, hacé click en el botón Entrar en la parte superior del sitio. Si no lo ves, ¡es porque ya estás logueado!
	En el formulario de login tenés que poner tu usuario y tu contraseña para que tu usuario sea identificado.
	Una vez que apretaste Entrar, te van a aparecer nuevos menúes en la barra superior, que son las acciones
	que podés realizar.
</p>
<br/>
<h3>Intercambios</h3>
<p>
	Le llamaremos intercambios tanto a ofertas como a pedidos.
</p>
<br/>
<h3>Ofertas</h3>
<p>
	Una oferta es cuando tenés algo que querés regalar. Al crear la oferta, se te pedirá un "punto de encuentro"
	que va a ser donde se realizará la donación (a menos que luego, en una conversación con quien reciba
	el regalo, decidas lo contrario, ¡pero eso queda ya por cuenta tuya!), que va a ser un lugar que le quede cómodo
	al donante. Recomendamos que el lugar no sea cerca de la casa del donante; lo mejor son lugares anónimos
	y concurridos como plazas, esquinas de avenidas, etc. para evitar algún posible robo u otra actividad delictiva.

	Al publicar la oferta, podés tener comentarios de otros usuarios. Podés hacer click en el botón "PM" que está a su
	lado para iniciar una conversación privada con ese usuario y de esta manera arreglar la entrega del regalo.
</p>
<br/>
<h3>Pedidos</h3>
<p>
	Un pedido es cuando necesitás algo y te gustaría obtenerlo gratis. Al crear el pedido, se te pedirá un "punto de encuentro"
	que va a ser donde se realizará la donación (a menos que luego, en una conversación con el donante, decidas lo contrario, ¡pero eso queda ya por cuenta tuya!), 
	que va a ser un lugar que te quede cómodo al donante. Recomendamos que el lugar no sea cerca de la casa del donante; lo mejor son lugares anónimos
	y concurridos como plazas, esquinas de avenidas, etc. para evitar algún posible robo u otra actividad delictiva.

	Al publicar el pedido, podés tener comentarios de posibles donantes. Podés hacer click en el botón "PM" que está a su
	lado para iniciar una conversación privada con ese usuario y de esta manera arreglar la entrega del regalo.
</p>
<br/>
<h3>Conversaciones</h3>
<p>
	Las conversaciones son cadenas de mensajes privados que tenés con otro usuario. Son el medio con el que podés arreglar la entrega del regalo o
	pasarle datos privados (mensajero, mail, etc.) para arreglar por otro medio.
</p>
<h3>Intercambio finalizado</h3>
<p>
	Una vez que hayas completado la entrega o el recibo del regalo, o cuando por alguna otra razón (no
	dispongas mas del objeto o lo hayas conseguido de otra forma) y quieras dar de baja tu oferta o pedido
	para que no aparezca mas en el mapa de búsqueda, lo único que tenés que hacer es ir a Cuentas / Mis Conversaciones
	y apretar "Finalizar".
</p>
<h3>Dudas, propuestas, opiniones</h3>
<p>
	Mandános un mail: <a href="mailto:<?=Configure::read('Mail.to')?>"><?=Configure::read('Mail.to')?></a>
</p>
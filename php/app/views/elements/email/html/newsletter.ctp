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
 * 
 */
?>

<style type="text/css">
    
</style>

<h2>Novedades de Guia Gratis</h2>

<?php foreach ($exchanges as $exchange) { ?>
<div class="exchange">
    <h4>
        <a href="<?php echo Configure::read('Host.url').'exchanges/view/'.$exchange['Exchange']['id'] ?>">
            <?php echo $exchange['Exchange']['title'] ?>
        </a>
    </h4>
    <p><?php echo $exchange['Exchange']['detail'] ?></p>
</div>
<?php } ?>

<p>Si desea dejar de recibir este correo debe loguearse a guia-gratis y cambiar la configuración
de su cuenta.</p>

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
<?php echo $this->element('gmap_default_values', array('start_point' => $start_point)); ?>
<?php $javascript->link('gmap', false); ?>
<?php $javascript->link('exchanges_set_location', false); ?>
<p>
    <?__("Seleccioná el punto de encuentro para realizar el intercambio")?>
</p>

<?php
    echo $form->label('Location: ');
    echo $form->text('address-field', array('id' => 'location'));
    echo $form->button('Go', array('type' => 'button', 'id' => 'go_button'));
?>
<div id="map"></div>
<?php
    echo $form->hidden('lat');
    echo $form->hidden('lng');
    echo "<div class='clear'></div>";
?>
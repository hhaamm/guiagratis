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

<script type="text/javascript">
   $(document).ready(
       function(){
           $("#location").bind("keypress", function(e) {
             if (e.keyCode == 13) {
                 $("#go_button").click();
                 return false;
            }
           })
        }
   )

   function markerMoved(){
        if(!marker_has_been_moved){
            $("#pleace-move-marker").show();
        }
        return marker_has_been_moved;
    }

</script>

<?php echo $this->element('gmap_default_values', array('start_point' => $start_point)); ?>
<?php $javascript->link('gmap', false); ?>
<?php $javascript->link('exchanges_set_location', false); ?>
<p>
    <?__("Seleccioná el punto de encuentro para realizar el intercambio")?>
</p>

<?php
    echo $form->label('Ubicación: ');
    echo $form->text('address-field', array('id' => 'location'));
    echo $form->button('Cambiar ubicación', array('type' => 'button', 'id' => 'go_button'));
?>

<div id="map"></div>
<?php
    echo $form->hidden('lat');
    echo $form->hidden('lng');
    echo $form->hidden('locality');
    echo $form->hidden('province');
    echo $form->hidden('country');
    echo $this->Html->div("clear");
    echo $this->Html->div("error-message","Mueve el marcador en el mapa para indicar un punto de encuentro",array("style"=>"display:none","id"=>"pleace-move-marker"));
?>

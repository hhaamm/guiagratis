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
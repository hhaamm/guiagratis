<?php $javascript->link("http://maps.google.com/maps?file=api&v=2&key=".Configure::read('GoogleMap.ApiKey'), false); ?>
<?php echo $this->element('gmap_default_values', array('start_point' => $start_point)); ?>
<?php $javascript->link('jquery.jec-1.2.5', false); ?>
<?php $javascript->link('gmap', false); ?>
<?php $javascript->link('exchanges_set_location', false); ?>
<div>
    <?__("Search your address or dragg your commerce to your real location")?>
</div>

<?php
    echo $form->label('Location: ');
    echo $form->text('address-field', array('id' => 'location'));
    echo $form->button('Go', array('type' => 'button', 'id' => 'go_button'));
?>
<div id="map"></div>
<?php
    echo $form->create('Delivery', array('action' => '/update_location', 'class' => 'delivery-form'));
    if ($is_visible)
	echo $form->input('Revision.comment', array('label' => 'Change reason'));
    else
	echo $form->hidden('Revision.comment', array('default' => 'Location set (auto-generated comment)'));
    echo $form->hidden('id', array('value' => $delivery['Delivery']['id']));
    echo $form->hidden('latitude');
    echo $form->hidden('longitude');
    echo "<div class='clear'></div>";
    echo $form->button('Done');
    echo $form->end();
?>
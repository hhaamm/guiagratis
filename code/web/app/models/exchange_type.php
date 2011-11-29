<?php

class ExchangeType extends AppModel {
	var $mongoSchema = array(
		'id'=>array('type'=>'integer'),
		'name'=>array('type'=>'string'),
		'description'=>array('type'=>'text')
	);
}
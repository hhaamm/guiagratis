<?php

class ExchangeState extends AppModel {
	var $mongoSchema = array(
		'id'=>array('type'=>'integer'),
		'name'=>array('type'=>'string'),
		'description'=>array('type'=>'text')
	);
}
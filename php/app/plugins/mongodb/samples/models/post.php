<?php

class Post extends AppModel {
	//var $useDbConfig = 'mongo';
	var $mongoSchema = array(
			'title' => array('type'=>'string'),
			'body'=>array('type'=>'string'),
			'hoge'=>array('type'=>'string'),
			'created'=>array('type'=>'date'),
			'modified'=>array('type'=>'date'),
			);	

}

?>

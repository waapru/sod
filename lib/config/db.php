<?php

return array(
	'shop_sod_date' => array(
		'id' => array('int', 11, 'unsigned' => 1, 'null' => 0, 'autoincrement' => 1),
		'order_id' => array('int', 11, 'null' => 0, 'default' => 0),
		'code' => array('varchar', 32, 'default' => ''),
		'date' => array('date', 'null' => 0),
		'start_hour' =>array('tinyint', 2, 'null' => 0, 'default' => '0'),
		'end_hour' =>array('tinyint', 2, 'null' => 0, 'default' => '0'),
		'user_date' => array('date', 'null' => 0),
		'user_start_hour' =>array('tinyint', 2, 'null' => 0, 'default' => '0'),
		'user_end_hour' =>array('tinyint', 2, 'null' => 0, 'default' => '0'),
		'ok' => array('tinyint', 1, 'null' => 0, 'default' => '0'),
		':keys' => array(
			'PRIMARY' => 'id',
		),
	),
);
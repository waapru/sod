<?php

return array(
	'name' => 'Дата доставки',
	'description' => '',
	'vendor' => '929600',
	'version' => '1.1.331',
	'img' => 'img/sod16.png',
	'shop_settings' => true,
	'frontend' => true,
	'handlers' => array(
		'backend_orders' => 'backendOrders',
		'backend_order' => 'backendOrder',
		'frontend_checkout' => 'frontendCheckout',
		'order_action.create' => 'orderActionCreate',
		'frontend_head' => 'frontendHead',
	),
);
//EOF
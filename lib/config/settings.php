<?php

return array(
	'on' => array(
		'title' => 'Включить плагин',
		'description' => '',
		'value' => true,
		'control_type' => waHtmlControl::CHECKBOX,
		'subject' => 'standart',
	),
	'frontend' => array(
		'title' => 'Включить плагин на витрине',
		'description' => '',
		'value' => true,
		'control_type' => waHtmlControl::CHECKBOX,
		'subject' => 'standart',
	),
	'weekend' => array(
		'title' => 'Работа в выходные',
		'description' => '',
		'value' => false,
		'control_type' => waHtmlControl::CHECKBOX,
		'subject' => 'standart',
	),
	'checkout' => array(
		'title' => 'Вывод формы через хук frontend_checkout',
		'description' => 'При отключенном выводе можно воспользоваться хелпером {shopSodPlugin::form()}',
		'value' => true,
		'control_type' => waHtmlControl::CHECKBOX,
		'subject' => 'standart',
	),
	'shipping' => array(
		'title' => 'Вывод формы только на шаге "Доставка"',
		'description' => '',
		'value' => true,
		'control_type' => waHtmlControl::CHECKBOX,
		'subject' => 'standart',
	),
	'necessary' => array(
		'title' => 'Покупатель обязан указать дату и время доставки',
		'description' => '',
		'value' => false,
		'control_type' => waHtmlControl::CHECKBOX,
		'subject' => 'standart',
	),
	'shipping_params' => array(
		'title' => 'Сохранять дату/время в параметрах заказа',
		'description' => 'Дата и время доставки сохраняются в параметрах заказа (shipping_params_date и shipping_params_time) для возможного использования в плагинах доставки',
		'value' => false,
		'control_type' => waHtmlControl::CHECKBOX,
		'subject' => 'standart',
	),
	'work_min_time' => array(
		'value' => 9,
		'control_type' => waHtmlControl::INPUT,
		'subject' => 'custom',
	),
	'work_max_time' => array(
		'value' => 18,
		'control_type' => waHtmlControl::INPUT,
		'subject' => 'custom',
	),
	'weekend_min_time' => array(
		'value' => 10,
		'control_type' => waHtmlControl::INPUT,
		'subject' => 'custom',
	),
	'weekend_max_time' => array(
		'value' => 16,
		'control_type' => waHtmlControl::INPUT,
		'subject' => 'custom',
	),
	'frontend_day_forward' => array(
		'value' => 3,
		'control_type' => waHtmlControl::INPUT,
		'subject' => 'custom',
	),
	'backend_day_forward' => array(
		'value' => 3,
		'control_type' => waHtmlControl::INPUT,
		'subject' => 'custom',
	),
	'frontend_cancel_day_forward' => array(
		'value' => 1,
		'control_type' => waHtmlControl::INPUT,
		'subject' => 'custom',
	),
	'backend_cancel_day_forward' => array(
		'value' => 0,
		'control_type' => waHtmlControl::INPUT,
		'subject' => 'custom',
	),
	'disable_states' => array(
		'title' => 'Скрыть форму для заказов со статусом"',
		'description' => '',
		'control_type' => waHtmlControl::GROUPBOX,
		'options_callback' => array('sodPlugin', 'settingOrderStatuses'),
		'subject' => 'states',
		'value' => array('deleted'),
	),
);
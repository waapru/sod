<?php

class shopSodPluginSetDateController extends waJsonController
{

	public function execute()
	{
		$frontend = ( wa()->getEnv() == 'frontend' );
		$settings = wa()->getPlugin('sod')->getSettings();
		
		$fday = ( $frontend ) ? $settings['frontend_day_forward'] : $settings['backend_day_forward'];
		$cday = ( $frontend ) ? $settings['frontend_cancel_day_forward'] : $settings['backend_cancel_day_forward'];
		
		$date =  waRequest::post('date',0);
		$p = '/([0-2]\d|3[01])\.(0\d|1[012])\.(\d{4})/';
		if ( $date && preg_match($p,$date,$m) )
		{
			$date_info = getdate(mktime(0,0,0,$m[2],$m[1],$m[3]));
			$is_weekend = ( $date_info['wday'] == 0 || $date_info['wday'] == 6 ) ? 1 : 0;
			
			if ( $is_weekend && !$settings['weekend'] )
				return $this->response['error'] = 1;
			
			$min = ( $is_weekend ) ? $settings['weekend_min_time'] : $settings['work_min_time'];
			$max = ( $is_weekend ) ? $settings['weekend_max_time'] : $settings['work_max_time'];
			
			$formated_date = implode('-',array_reverse(explode('.',$date)));
			
			$hour = waRequest::post('start_hour',$min);
			$start_hour = ( $hour > $min && $hour < $max ) ? $hour : $min;
			
			$hour = waRequest::post('end_hour',$max);
			$end_hour = ( $hour > $min && $hour < $max ) ? $hour : $max;
		}
		else
			return $this->response['error'] = 2;
		
		$data = array(
			'date' => $formated_date,
			'start_hour' => $start_hour,
			'end_hour' => $end_hour,
			'user_date' => $formated_date,
			'user_start_hour' => $start_hour,
			'user_end_hour' => $end_hour,
			'ok' => waRequest::post('ok',0) ? 1 : 0
		);
		
		$model = new shopSodPluginDateModel;
		if ( $frontend )
		{
			$cart = new shopCart;
			if ( false === $model->saveByCode($data,$cart->getCode()) )
				return $this->response['error'] = 3;
		}
		else
		{
			foreach ( array('user_date','user_start_hour','user_end_hour') as $f )
				unset($data[$f]);
			$order_id = waRequest::post('order_id',0);
			if ( false === $model->saveByOrderID($data,$order_id) )
				return $this->response['error'] = 3;
		}
		
		$this->response = $data;
	}

}

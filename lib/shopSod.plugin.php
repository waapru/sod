<?php

// $mysqldate = date( 'Y-m-d H:i:s', $phpdate );
// $phpdate = strtotime( $mysqldate );

class shopSodPlugin extends shopPlugin
{
	/* event: backend_orders */
	public function backendOrders($params)
	{
		$html = '';
		
		if ( $this->getSettings('on') )
		{
			$view = wa()->getView();
			$html = $view->fetch($this->path.'/templates/backend_orders.html');
		}
		return array(
			'sidebar_top_li' => $html
		);
	}
	
	/* event: backend_order */
	public function backendOrder($params)
	{
		$html = '';
		$state = $params['state']->getId();
		if ( $this->getSettings('on') && !in_array($state,$this->getSettings('disable_states')) )
		{
			$view = wa()->getView();
			$view->assign('settings',$this->getSettings());
			$view->assign('order_id',$params['id']);
			$model = new shopSodPluginDateModel;
			
			if ( $delivary_date = $model->getByField('order_id',$params['id']) )
				foreach ( array('date','user_date') as $k )
					$delivary_date[$k] = date('d.m.Y', strtotime($delivary_date[$k]));
			
			$view->assign('delivary_date',$delivary_date);
			$html = $view->fetch($this->path.'/templates/backend_order.html');
		}
		return array(
			'info_section' => $html
		);
	}
	
	/* event: frontend_checkout */
	public function frontendCheckout($data)
	{
		$html = '';
		$s = $this->getSettings();
		if ( $s['on'] && $s['checkout'] && (($s['shipping'] && $data['step'] == 'shipping') || !$s['shipping']) )
			$html = self::form();
		return $html;
	}
	
	
	/* event: frontend_head */
	public function frontendHead($data)
	{
		if ( $this->getSettings('on') )
		{
			$response = waSystem::getInstance()->getResponse();
			$aurl = 'plugins/sod/js/datetimepicker/';
			$response->addCss($aurl.'jquery.datetimepicker.css','shop');
			$response->addJs($aurl.'jquery.datetimepicker.js','shop');
			
			$f = new shopSodPluginFiles;
			$f->addCss('css');
			$f->addJs('js');
		}
	}
	
	
	/* event: order_action.create */
	public function orderActionCreate($data)
	{
		$order_id = $data['order_id'];

		$cart = new shopCart;
		$code = $cart->getCode();
		$model = new shopSodPluginDateModel;

		if ( $row = $model->getByField('code',$code) )
		{
			$id = $row['id'];
			$model->updateById($id,array('order_id'=>$order_id,'code'=>'_'));
			
			if ( $this->getSettings('shipping_params') )
			{
				$order_params_model = new shopOrderParamsModel;
				$order_params_model->set($order_id,array(
					'shipping_params_date' => date( 'Y-m-d', strtotime( $row['user_date'] ) ),
					'shipping_params_time' => $row['user_start_hour'].'-'.$row['user_end_hour'],
					'shipping_params_agreed' => 0,
				),false);
			}
		}
	}
	
	
	/* helper: form */
	static function form()
	{
		$plugin = wa('shop')->getPlugin('sod');
		$s = $plugin->getSettings();

		$html = '';
		if ( $s['on'] && $s['frontend'] )
		{
			$model = new shopSodPluginDateModel;
			$data = $model->getByCode();
			if ( !$data )
			{
				$c = $s['frontend_cancel_day_forward'];
				$time = strtotime("+$c day");
				$date_info = getdate($time);
				$w = $date_info['wday'];
				$is_weekend = ( $w == 0 || $w == 6 ) ? 1 : 0;
				
				if ( !$s['weekend'] && $is_weekend )
				{
					$c += $w == 0 ? 1 : 2;
					$time = strtotime("+$c day");
					$is_weekend = 0;
				}
				$data = array(
					'date' => date("d.m.Y",$time),
					'start' => $is_weekend  ? $s['weekend_min_time'] : $s['work_min_time'],
					'end' => $is_weekend  ? $s['weekend_max_time'] : $s['work_max_time']
				);
				
				$n = date("Y-m-d",$time);
				$cart = new shopCart;
				$model->insert(array(
					'date' => $n,
					'start_hour' => $data['start'],
					'end_hour' => $data['end'],
					'user_date' => $n,
					'user_start_hour' => $data['start'],
					'user_end_hour' => $data['end'],
					'code' => $cart->getCode(),
					'order_id' => 0,
					'ok' => 0
				));
			}
			$data['settings'] = $s;

			$view = wa()->getView();
			$view->assign($data);
			$f = new shopSodPluginFiles;
			$html = $view->fetch('string:'.$f->getFileContent('checkout'));
		}
		return $html;
	}
	
	
	public function getPath()
	{
		return $this->path;
	}
	
	
	/* helper: dateTime */
	static function dateTime($order_id = 0,$html = '')
	{
		if ( empty($html) )
			$html = 'Дата доставки заказа #date# с #start# до #end#';
		$order_id = (int)$order_id;
		$model = new shopSodPluginDateModel;
		
		$r = false;
		if ( $order_id )
			$r = $model->getByOrderId($order_id);
		
		if ( !$r )
			$r = $model->getByCode();
		
		if ( $r )
		{
			$mask = $data = array();
			foreach ( array('date','start','end') as $k )
			{
				$mask[] = "#$k#";
				$data[] = ( isset($r[$k]) ) ? $r[$k] : '';
			}
			
			$html = str_replace($mask,$data,$html);
		}
		else
			$html = '';
		return $html;
	}
	
	
	//Для уведомлений
	static function dateTimeN($order_id,$html = '')
	{
		$c = '';
		$order_id = shopHelper::decodeOrderId($order_id);
		if ( !empty($order_id) )
			$c = self::dateTime($order_id,$html);
		return $c;
	}
	
	
	// options_callback for disable_states
	static function settingOrderStatuses()
	{
		$workflow = new shopWorkflow();
		return $workflow->getAllStates();
	}
}
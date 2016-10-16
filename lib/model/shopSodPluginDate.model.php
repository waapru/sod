<?php

class shopSodPluginDateModel extends waModel
{
	protected $table = 'shop_sod_date';
	protected $_dates_limit = 0;
	
	public function saveByOrderID($data,$order_id)
	{
		$order_id = (int)$order_id;
		if ( !$order_id )
			return false;
		
		if ( wa('shop')->getPlugin('sod')->getSettings('shipping_params') )
		{
			$order_params_model = new shopOrderParamsModel;
			$order_params_model->set($order_id,array(
				'shipping_params_date' => date( 'Y-m-d', strtotime( $data['date'] ) ),
				'shipping_params_time' => $data['start_hour'].'-'.$data['end_hour'],
				'shipping_params_agreed' => $data['ok'],
			),false);
		}
		
		if ( $this->countByField('order_id',$order_id) )
			return $this->updateByField('order_id',$order_id,$data);
		else
		{
			$data['order_id'] = $order_id;
			return $this->insert($data);
		}
	}
	
	
	public function saveByCode($data,$code)
	{
		if ( empty($code) )
			return false;
		
		if ( $this->countByField('code',$code) )
			return $this->updateByField('code',$code,$data);
		else
		{
			$data['code'] = $code;
			return $this->insert($data);
		}
	}
	
	
	public function getByCode()
	{
		$cart = new shopCart;
		$code = $cart->getCode();
		$fields = "
			DATE_FORMAT(user_date, '%d.%m.%Y') 'date',
			user_start_hour 'start',
			user_end_hour 'end'
		";
		$w = "code LIKE '".$this->escape($code)."'";
		return $this->select($fields)->where($w)->fetch();
	}
	
	
	public function getByOrderId($order_id)
	{
		$order_id = (int)$order_id;
		if ( !$order_id )
			return false;
		
		$fields = "
			DATE_FORMAT(date, '%d.%m.%Y') 'date',
			start_hour 'start',
			end_hour 'end'
		";
		$w = 'order_id = '.$order_id;
		
		return $this->select($fields)->where($w)->fetch();
	}
	
	
	public function getDateList()
	{
		$states = implode("','",wa('shop')->getPlugin('sod')->getSettings('disable_states'));
		
		$list = $this->_getDateList('total',$states);
		$yes = $this->_getDateList('yes',$states);
		$not = $this->_getDateList('not',$states);
		
		$time = time();
		$p = '/([0-2]\d|3[01])\.(0\d|1[012])\.(\d{4})/';
		if ( !empty($list) )
			foreach ( $list as $date=>&$v )
			{
				$v['y'] = isset($yes[$date]) ? $yes[$date]['y'] : 0;
				$v['n'] = isset($not[$date]) ? $not[$date]['n'] : 0;
				$v['f'] = ( preg_match($p,$date,$m) && mktime(0,0,0,$m[2],$m[1],$m[3]) >= $time ) ? 1 : 0;
				$v['w'] = date('d.m.Y',$time) == $date ? 1 : 0;
			}
		
		return $list;
	}
	
	
	private function _getDateList($ok = 'total', $states)
	{
		switch ( $ok )
		{
			case 'yes' :
				$ok_field = 'y';
				$ok_where = 'AND s.ok = 1';
				break;
			case 'not' :
				$ok_field = 'n';
				$ok_where = 'AND s.ok = 0';
				break;
			case 'total' :
			default :
				$ok_field = 'total';
				$ok_where = '';
		}
		
		$q = "
			SELECT
			  COUNT(*) AS $ok_field,
			  DATE_FORMAT(s.date, '%d.%m.%Y') AS formated_date,
			  s.date
			FROM shop_sod_date s
			  INNER JOIN shop_order o
				ON s.order_id = o.id
			WHERE o.state_id NOT IN ('$states') $ok_where
			GROUP BY s.date
			ORDER BY s.date DESC
		";
		return $this->query($q)->fetchAll('formated_date');
	}
	
	
	public function setCodeEmpty($order_id)
	{
		$order_id = (int)$order_id;
		if ( $order_id )
			$this->query("UPDATE {$this->table} SET code = '' WHERE order_id = $order_id")->fetch();
	}
}
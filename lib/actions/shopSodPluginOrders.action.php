<?php

class shopSodPluginOrdersAction extends shopOrderListAction
{
	public function __construct($params=null) {
		parent::__construct($params);
		$this->collection = new shopSodPluginOrdersCollection($this->getHash());
	}
	
	public function execute()
	{
		$config = $this->getConfig();

		$default_view = $config->getOption('orders_default_view');
		$view = waRequest::get('view', $default_view, waRequest::TYPE_STRING_TRIM);

		$date = waRequest::post('date');
		
		$orders = $this->getOrders(0, $this->getCount(),$date);
		
		$action_ids = array_flip(array('process', 'pay', 'ship', 'complete', 'delete', 'restore'));
		$workflow = new shopWorkflow();
		$actions = array();
		foreach ( $workflow->getAllActions() as $action )
			if ( isset($action_ids[$action->id]) )
				$actions[$action->id] = array(
					'name' => $action->name,
					'style' => $action->getOption('style')
				);
		
		$state_names = array();
		foreach ($workflow->getAvailableStates() as $state_id => $state) {
			$state_names[$state_id] = $state['name'];
		}
		
		$counters = array(
			'state_counters' => array(
				'new' => $this->model->getStateCounters('new')
			)
		);
		
		$this->assign(array(
			'orders' => array_values($orders),
			'total_count' => $this->getTotalCount(),
			'count' => count($orders),
			'order' => $this->getOrder($orders),
			'currency' => $this->getConfig()->getCurrency(),
			'state_names' => $state_names,
			'params' => $this->getFilterParams(),
			'params_str' => $this->getFilterParams(true),
			'view' => $view,
			'timeout' => $config->getOption('orders_update_list'),
			'actions' => $actions,
			'counters' => $counters
		));
	}

	public function getOrder($orders)
	{
		$order_id = waRequest::get('id', null, waRequest::TYPE_INT);
		if ($order_id) {
			if (isset($orders[$order_id])) {
				return $orders[$order_id];
			} else {
				$item = $this->model->getById($order_id);
				if (!$item) {
					throw new waException("Unknown order", 404);
				}
				return $item;
			}
		} else if (!empty($orders)) {
			reset($orders);
			return current($orders);
		}
		return null;
	}
	
	public function getOrders($offset, $limit, $date = null)
	{
		if ($this->orders === null)
		{
			$data = array(
				'table' => 'shop_sod_date',
				'on' => 'o.id = :table.order_id',
			);
			$this->collection->addJoin($data);
			$this->collection->orderBy('date','DESC','ssd1');
			
			$states = implode("','",wa('shop')->getPlugin('sod')->getSettings('disable_states'));
			$this->collection->addWhere("o.state_id NOT IN ('$states')");
			
			$this->orders = $this->collection->getOrders("*,items,contact,params", $offset, $limit,true,$date);
			
			$this->extendContacts($this->orders);
			shopHelper::workupOrders($this->orders);
		}
		return $this->orders;
	}
}
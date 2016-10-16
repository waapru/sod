<?php

class shopSodPluginOrdersCollection extends shopOrdersCollection
{
	public function orderBy($field, $order = 'ASC', $alias = 'o')
	{
		$this->order_by = "{$alias}.{$field} {$order}";
	}
	
	
	public function getOrders($fields = "*", $offset = 0, $limit = null, $escape = true, $date = false)
	{
		if (is_bool($limit)) {
			$escape = $limit;
			$limit = null;
		}
		if ($limit === null) {
			if ($offset) {
				$limit = $offset;
				$offset = 0;
			} else {
				$limit = 50;
			}
		}

		if ( $date )
			$this->where[] = "ssd1.date = '$date'";
		
		$sql = $this->getSQL();
		
		$_fields = $this->getFields($fields);
		$other_fields = array();
		if ( is_array($_fields) )
		{
			list($fields,$postprocess_fields) = $this->getFields($fields);
			$fields_sql = ', o.*';
			$other_fields = $postprocess_fields;
		}
		else
		{
			$fields_sql = ', '.$this->getFields($fields);
			$other_fields = $this->other_fields;
		}
			
		$sql = "SELECT ssd1.*, DATE_FORMAT(ssd1.date,'%e.%m.%Y') formated_date $fields_sql ".$sql;
		$sql .= " LIMIT ".($offset ? $offset.',' : '').(int) $limit;
		
		$data = $this->getModel()->query($sql)->fetchAll('id');
		if (!$data) {
			return array();
		}

		$ids = array_keys($data);
		
		// add other fields
		foreach ($other_fields as $field) {
			switch ($field) {
				case 'items':
				case 'params':
					$rows = $this->getModel($field)->getByField('order_id', $ids, true);
					foreach ($rows as $row) {
						if ($field == 'params') {
							$data[$row['order_id']][$field][$row['name']] = $row['value'];
						} else {
							if ($escape) {
								$row['name'] = htmlspecialchars($row['name']);
							}
							$data[$row['order_id']][$field][] = $row;
						}
					}
					break;
				case 'contact':
					$contact_ids = array();
					foreach ($data as $o) {
						$contact_ids[] = $o['contact_id'];
					}
					$contact_model = new waContactModel();
					$contacts = $contact_model->getById(array_unique($contact_ids));
					foreach ($data as &$o) {
						if (isset($contacts[$o['contact_id']])) {
							$c = $contacts[$o['contact_id']];
							$o['contact'] = array(
								'id' => $c['id'],
								'name' => $c['name'],
								'photo' => $c['photo']
							);
							if ($escape) {
								$o['contact']['name'] = htmlspecialchars($c['name']);
							}
						}
					}
					unset($o);
					break;
			}
		}
		unset($t);
		
		return $data;
	}
	
	
	public function getOrderOffset($order,$date = null)
	{
		$model = $this->getModel();
		
		if (!is_array($order)) {
			$order_id = (int) $order;
			$order = $model->getById($order_id);
		}
		$order_id = (int) $order_id;
		if (!$order) {
			return false;
		}
		$date = $model->escape($date);

		// for calling prepare
		$this->getSQL();
		
		// first, check existing in collection
		$this->where[] = 'o.id = '.(int) $order['id'];
		$sql = "SELECT * ".$this->getSQL();
		if (!$model->query($sql)) {
			return false;
		}
		array_pop($this->where);
		
		// than calculate offset
		$this->where[] = "(ssd1.date = '{$date}' AND o.id < '{$order_id}')";
		$sql = "SELECT COUNT(o.id) offset ".$this->getSQL();
		$offset = $model->query($sql)->fetchField();
		array_pop($this->where);
		
		return $offset;
		
	}
}
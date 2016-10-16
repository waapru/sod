<?php

class shopSodPluginDatesAction extends waViewAction
{
	public function execute()
	{
		$model = new shopSodPluginDateModel;
		$this->view->assign('date_list',$model->getDateList());
	}
}
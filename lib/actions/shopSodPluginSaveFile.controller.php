<?php

class shopSodPluginSaveFileController extends waJsonController
{

	public function execute()
	{
		$theme = waRequest::post('theme','');
		$f = new shopSodPluginFiles($theme);
		$f->saveFromPostData();
	}

}
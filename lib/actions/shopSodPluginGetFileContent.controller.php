<?php

class shopSodPluginGetFileContentController extends waJsonController
{

	public function execute()
	{
		$theme = waRequest::post('theme','');
		$name = waRequest::post('name','');
		
		$f = new shopSodPluginFiles($theme);
		$this->response = $f->getFileContent($name);
	}

}
<?php

class shopSodPluginSettingsAction extends waViewAction
{
	public function execute()
	{
		$plugin_id = 'sod';
		$plugin = wa()->getPlugin($plugin_id);
		
		$settings = array();
		foreach ( array('standart') as $t )
		{
			$controls = array(
				'subject' => $t,
				'namespace' => 'shop_'.$plugin_id,
				'title_wrapper' => '%s',
				'description_wrapper' => '<br><span class="hint">%s</span>',
				'control_wrapper'     => '<div class="field"><div class="name">%s</div><div class="value">%s%s</div></div>',
			);
			$settings[$t] = implode('',$plugin->getControls($controls));
		}
		
		$settings['values'] = $plugin->getSettings();
		$states = shopSodPlugin::settingOrderStatuses();
		
		$v = $plugin->getVersion();
		$f = new shopSodPluginFiles;
		$themes = $f->getThemes();
		$this->view->assign(compact('settings','themes','states','v'));
		
		
		
		
		// $path = 'plugins/'.$plugin_id.'/';
		
		// $response = $this->getResponse();
		// $response->addJs($path.'js/jquery.waapplugindisign.js','shop');
		// $response->addCss($path.'css/jquery.waapplugindisign.css','shop');
		// $response->addJs($path.'js/jquery.waapplugindesc.js','shop');
		// $response->addCss($path.'css/jquery.waapplugindesc.css','shop');
		// $response->addJs($path.'js/datetimepicker/jquery.datetimepicker.js','shop');
		// $response->addCss($path.'js/datetimepicker/jquery.datetimepicker.css','shop');
		// $this->view->assign('js',$response->getJs(true,true));
		// $this->view->assign('css',$response->getCss(true,true));
		
		// $standart_settings = $plugin->getControls(array(
			// 'subject' => 'standart',
			// 'namespace' => 'shop_sod',
			// 'title_wrapper' => '%s',
			// 'description_wrapper' => '<br><span class="hint">%s</span>',
			// 'control_wrapper'     => '<div class="field"><div class="name">%s</div><div class="value">%s%s</div></div>',
		// ));
		
		// $f = new shopSodPluginFiles;
		// $this->view->assign(array(
			// 'themes' => $f->getThemes(),
			// 'settings' => $plugin->getSettings(),
			// 'states' => shopSodPlugin::settingOrderStatuses(),
			// 'standart_settings' => implode('',$standart_settings),
		// ));
	}
}
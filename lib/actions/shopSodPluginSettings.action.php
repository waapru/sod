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
	}
}
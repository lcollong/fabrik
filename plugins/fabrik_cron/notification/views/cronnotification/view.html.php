<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class fabrikViewCronnotification extends JView
{

	function display($tmpl = 'default')
	{
		$this->assignRef('rows', $this->get('UserNotifications'));
		$viewName = $this->getName();
		$tmplpath = JPATH_ROOT . '/fabrik_cron/notification/views/cronnotification/tmpl/' . $tmpl;
		$this->_setPath('template', $tmplpath);
		FabrikHelperHTML::stylesheetFromPath('fabrik_cron/notification/views/cronnotification/tmpl/' . $tmpl. '/template.css');
		echo parent::display();
	}

}
?>

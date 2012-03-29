<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class fabrikViewPivotpopupwin extends JView
{

	function display($tmpl = 'default')
	{
		$this->assignRef('data', $this->get('Pivot'));
		$this->assign('heading', $this->get('Heading'));
		$tmplpath = JPATH_ROOT . '/plugins/fabrik_list/pivot/views/pivotpopupwin/tmpl/' . $tmpl;
		$this->_setPath('template', $tmplpath);
		
		$qs = '?c=pivot';
		$qs .= '&amp;buttoncount=0';
		FabrikHelperHTML::stylesheet(COM_FABRIK_LIVESITE.'/plugins/fabrik_list/pivot/views/pivotpopupwin/tmpl/' . $tmpl . '/template_css.php'.$qs);
		
		return parent::display();
	}

}
?>
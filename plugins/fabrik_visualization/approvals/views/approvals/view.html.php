<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class fabrikViewApprovals extends JView
{

	function display($tmpl = 'default')
	{
		FabrikHelperHTML::framework();
		$model = $this->getModel();
		$usersConfig = JComponentHelper::getParams('com_fabrik');
		$id = JRequest::getVar('id', $usersConfig->get('visualizationid', JRequest::getInt('visualizationid', 0)));
		$model->setId($id);
		$this->assign('id', $id);
		$this->assignRef('row', $this->get('Visualization'));
		$this->assign('rows', $this->get('Rows'));

		$this->assign('containerId', $this->get('ContainerId'));

		$this->calName = $this->get('VizName');

		$this->assignRef('params', $this->get('PluginParams'));
		$tmpl = $this->params->get('approvals_layout', $tmpl);
		$tmplpath = JPATH_SITE . '/plugins/fabrik_visualization/approvals/views/approvals/tmpl/' . $tmpl;
		$this->_setPath('template', $tmplpath);

		FabrikHelperHTML::stylesheetFromPath('plugins/fabrik_visualization/approvals/views/approvals/tmpl/' . $tmpl . '/template.css');
		FabrikHelperHTML::script('plugins/fabrik_visualization/approvals/approvals.js', "var approvals = new fbVisApprovals('approvals_".$id."');");

		$text = $this->loadTemplate();
		$opt = JRequest::getVar('option');
		$view = JRequest::getCmd('view');
		JRequest::setVar('view', 'article');
		JRequest::setVar('option', 'com_content');
		jimport('joomla.html.html.content');
		$text .= '{emailcloak=off}';
		$text = JHTML::_('content.prepare', $text);
		$text = preg_replace('/\{emailcloak\=off\}/', '', $text);
		JRequest::setVar('option', $opt);
		echo $text;
	}


}
?>

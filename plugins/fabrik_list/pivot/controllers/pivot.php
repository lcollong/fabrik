<?php
/**
 * @package Joomla
 * @subpackage Fabrik
 * @copyright Copyright (C) 2005 Rob Clayburn. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

//require_once(COM_FABRIK_FRONTEND . '/helpers/params.php');
require_once(COM_FABRIK_FRONTEND . '/helpers/string.php');

/**
 * Pivot list plug-in Controller
 *
 * @static
 * @package		Joomla
 * @subpackage	Fabrik
 * @since 1.5
 */

class FabrikControllerListpivot extends JController
{
	/** @var string path of uploaded file */
	var $filepath = null;

	/**
	 * default display mode
	 *
	 * @return unknown
	 */

	function display()
	{
		echo "display";
	}

	/**
	 * set up the popup window containing the form to create the
	 * pivot view
	 *
	 * @return string html
	 */

	function popupwin()
	{
		$document = JFactory::getDocument();
		$viewName = 'pivotpopupwin';

		$viewType = $document->getType();

		// Set the default view name from the Request
		$view = $this->getView($viewName, $viewType);

		$listModel = JModel::getInstance('list', 'FabrikFEModel');
		$listModel->setId(JRequest::getInt('id'));
		$formModel = $listModel->getFormModel();
		// Push a model into the view
		$pluginManager = JModel::getInstance('Pluginmanager', 'FabrikFEModel');
		$model = $pluginManager->getPlugIn('pivot', 'list');
		
		$model->formModel = $formModel;
		$model->listModel = $listModel;
		$model->setParams($listModel->getParams(), JRequest::getInt('renderOrder'));
		if (!JError::isError($model))
		{
			$view->setModel($model, true);
		}
		$view->setModel($listModel);
		$view->setModel($formModel);
		
		// Display the view
		$view->assign('error', $this->getError());
		return $view->display();
	}

}
?>
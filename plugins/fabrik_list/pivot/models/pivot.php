<?php

/**
* Add an action button to the table to show a pivot view
* @package Joomla
* @subpackage Fabrik
* @author Rob Clayburn
* @copyright (C) Rob Clayburn
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

//require the abstract plugin class
require_once(COM_FABRIK_FRONTEND . '/models/plugin-list.php');
require_once(COM_FABRIK_FRONTEND . '/helpers/html.php');

class plgFabrik_ListPivot extends plgFabrik_List {

	protected $buttonPrefix = 'pivot';

	/**
	 * get the pivot view heading
	 */

	public function getHeading()
	{
		$model = $this->formModel->getTableModel();
		$params = $model->getParams();
		$renderOrder = JRequest::getInt('renderOrder');
		return JArrayHelper::getValue($params->get('pivot_heading'), $renderOrder, 'Pivot view');
	}

	/**
	 * create the pivot data
	 * @return array of objects - first being the headings, subsequent the data
	 */

	public function getPivot()
	{
		$model = $this->formModel->getListModel();
		
		$renderOrder = JRequest::getInt('renderOrder');
		$params = $model->getParams();
		$val = FabrikString::safeColName(JArrayHelper::getValue($params->get('pivot_value'), $renderOrder));
		$xCol = FabrikString::safeColName(JArrayHelper::getValue($params->get('pivot_xcol'), $renderOrder));
		$yColName = JArrayHelper::getValue($params->get('pivot_ycol'), $renderOrder);
		$yCol = FabrikString::safeColName($yColName);

		$element = $model->getFormModel()->getElement($yColName);
		$db = $model->getDb();
		$table = $model->getTable();
		$join = $model->buildQueryJoin();
		$where = $model->buildQueryWhere();

		/* $db->setQuery("SELECT DISTINCT $yCol FROM $table->db_table_name");
		$yCols = $db->loadResultArray();

		$query = "select $xCol, \n";
		$data = array();
		foreach ($yCols as $c)
		{
			$data[] = "SUM(IF(" . $yCol . "=" . $db->quote($c) . "," . $val . ",NULL)) AS " .$db->quote($element->getLabelForValue($c)) . "\n";
		}
		$query .= implode(",", $data);
		$query .= "\nFROM " . $table->db_table_name . " $join $where group by $xCol"; */
		
		$db->setQuery("SELECT DISTINCT $xCol FROM $table->db_table_name");
		$xCols = $db->loadResultArray();
		
		$query = "select $yCol, \n";
		$data = array();
		foreach ($xCols as $c)
		{
		$data[] = "SUM(IF(" . $xCol . "=" . $db->quote($c) . "," . $val . ",NULL)) AS " .$db->quote($element->getLabelForValue($c)) . "\n";
		}
		$query .= implode(",", $data);
		$query .= "\nFROM " . $table->db_table_name . " $join $where group by $yCol";
		

		$db->setQuery($query);
		if (!$data = $db->loadObjectList())
		{
			JError::raiseError(500, $db->getErrorMsg());
		}

		$headings = JArrayHelper::toObject(array_keys(JArrayHelper::fromObject($data[0])));
		array_unshift($data, $headings);
		return $data;
	}

	function onGetPluginRowHeadings()
	{
		return "pivot";
	}
/* 	function button()
	{
		return "pivot";
	} */
	
	protected function buttonLabel()
	{
		return $this->getParams()->get('pivot_button_label', parent::buttonLabel());
	}

	/**
	 * parameter name which defines which user group can access the pivot view
	 */

	function getAclParam()
	{
		return 'pivot_access';
	}

	/**
	 * determine if the list plugin is a button and can be activated only when rows are selected
	 *
	 * @return	bool
	 */

	function canSelectRows()
	{
		return false;//return $this->canUse();
	}

	/**
	 * return the javascript to create an instance of the class defined in formJavascriptClass
	 * @param	object	parameters
	 * @param	object	list model
	 * @param	array	[0] => string list form id to contain plugin
	 * @return	bool
	 */

	function onLoadJavascriptInstance($params, $model, $args)
	{
		$opts = $this->getElementJSOptions($model);
		$opts->liveSite = COM_FABRIK_LIVESITE;
		$opts->renderOrder = $this->renderOrder;
		$opts->title = $params->get('pivot_heading', 'Pivot');
		$opts = json_encode($opts);
		$this->jsInstance = "new FbListPivot($opts)";
		return true;
	}

}
?>
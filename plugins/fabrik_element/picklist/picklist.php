<?php
/**
 * Plugin element to two lists - one to select from the other to select into
 * @package fabrikar
 * @author Rob Clayburn
 * @copyright (C) Rob Clayburn
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

require_once(JPATH_SITE . '/components/com_fabrik/models/element.php');

class plgFabrik_ElementPicklist extends plgFabrik_ElementList
{

	public function setId($id)
	{
		parent::setId($id);
		$params = $this->getParams();
		//set elementlist params from picklist params
		$params->set('allow_frontend_addto', (bool)$params->get('allowadd', false));
	}

	/**
	 * (non-PHPdoc)
	 * @see plgFabrik_ElementList::render()
	 */

	function render($data, $repeatCounter = 0)
	{
		$name = $this->getHTMLName($repeatCounter);
		$id	= $this->getHTMLId($repeatCounter);
		$element = $this->getElement();
		$params = $this->getParams();
		$arVals = $this->getSubOptionValues();
		$arTxt = $this->getSubOptionLabels();
		$arSelected = (array)$this->getValue($data, $repeatCounter);
		$errorCSS = (isset($this->_elementError) &&  $this->_elementError != '') ?  " elementErrorHighlight" : '';
		$attribs = 'class="picklistcontainer'.$errorCSS."\"";
		$style = ".frompicklist, .topicklist{\n"
		."background-color:#efefef;\n"
		."padding:5px !important;\n"
		."}\n"
		."\n"
		."div.picklistcontainer{\n"
		."width:40%;\n"
		."margin-right:10px;\n"
		."margin-bottom:10px;\n"
		."float:left;\n"
		."}\n"
		."\n"
		.".frompicklist li, .topicklist li, li.picklist{\n"
		."background-color:#FFFFFF;\n"
		."margin:3px;\n"
		."padding:5px !important;\n"
		."cursor:move;\n"
		."}\n"
		."\n"
		."li.emptyplicklist{\n"
		."background-color:transparent;\n"
		."cursor:pointer;\n"
		."}";
		FabrikHelperHTML::addStyleDeclaration($style);
		$i = 0;
		$aRoValues = array();
		$fromlist = array();
		$tolist = array();
		$fromlist[] = 'from:<ul id="' . $id      . '_fromlist" class="frompicklist">';
		$tolist[] = 'to:<ul id="' . $id . '_tolist" class="topicklist">';
		foreach ($arVals as $v)
		{
			//$tmptxt = addslashes(htmlspecialchars($arTxt[$i]));
			if (!in_array($v, $arSelected))
			{
				$fromlist[] = '<li id="' . $id . '_value_' . $v . '" class="picklist">' . $arTxt[$i] . '</li>';
			}
			$i ++;
		}
		$i = 0;
		$lookup = array_flip($arVals);
		foreach ($arSelected as $v)
		{
			if ($v == '' || $v == '-')
			{
				continue;
			}
			$k = JArrayHelper::getValue($lookup, $v);
			$tmptxt = addslashes(htmlspecialchars(JArrayHelper::getValue($arTxt, $k)));
			$tolist[] = '<li id="' . $id . '_value_' . $v . '" class="' . $v . '">'. $tmptxt . '</li>';
			$aRoValues[] = $tmptxt;
			$i ++;
		}
		if (empty($arSelected))
		{
			$fromlist[] = '<li class="emptyplicklist">' . JText::_('PLG_ELEMENT_PICKLIST_DRAG_OPTIONS_HERE') . '</li>';
		}
		if (empty($aRoValues))
		{
			$tolist[] = '<li class="emptyplicklist">' . JText::_('PLG_ELEMENT_PICKLIST_DRAG_OPTIONS_HERE') . '</li>';
		}

		$fromlist .= "</ul>\n";
		$tolist .= "</ul>\n";

		$str = "<div $attribs>" . implode("\n", $fromlist) . "</div><div class='picklistcontainer'>" . implode("\n", $tolist) . "</div>";
		$str .=  $this->getHiddenField($name, json_encode($arSelected), $id);
		if (!$this->editable)
		{
			return implode(', ', $aRoValues);
		}
		$str .= $this->getAddOptionFields($repeatCounter);
		return $str;
	}

	/**
	 * (non-PHPdoc)
	 * @see plgFabrik_Element::elementJavascript()
	 */

	function elementJavascript($repeatCounter)
	{
		$id = $this->getHTMLId($repeatCounter);
		$element = $this->getElement();
		$data = $this->getFormModel()->_data;
		$arVals = $this->getSubOptionValues();
		$arTxt = $this->getSubOptionLabels();
		$params = $this->getParams();
		$opts = $this->getElementJSOptions($repeatCounter);
		$opts->allowadd = (bool)$params->get('allowadd', false);
		$opts->defaultVal = $this->getValue($data, $repeatCounter);;
		//$opts->data = array_combine($arVals, $arTxt);;
		$opts->hovercolour = $params->get('picklist-hovercolour', '#AFFFFD');
		$opts->bghovercolour = $params->get('picklist-bghovercolour', '#FFFFDF');
		$opts = json_encode($opts);
		JText::script('PLG_ELEMENT_PICKLIST_ENTER_VALUE_LABEL');
		return "new FbPicklist('$id', $opts)";
	}

	/**
	 * Get the sql for filtering the table data and the array of filter settings
	 * @param	string	filter value
	 * @return	string	filter value
	 */

	function prepareFilterVal($val)
	{
		$arVals = $this->getSubOptionValues();
		$arTxt 	= $this->getSubOptionLabels();
		for ($i = 0; $i<count($arTxt); $i++)
		{
			if (strtolower($arTxt[$i]) == strtolower($val))
			{
				$val =  $arVals[$i];
				return $val;
			}
		}
		return $val;
	}

	/**
	 * this builds an array containing the filters value and condition
	 * @param	string	initial $value
	 * @param	string	intial $condition
	 * @param	string	eval - how the value should be handled
	 * @return	array	(value condition)
	 */

	function getFilterValue($value, $condition, $eval )
	{
		$value = $this->prepareFilterVal($value);
		$return = parent::getFilterValue($value, $condition, $eval);
		return $return;
	}

	/**
	 * (non-PHPdoc)
	 * @see plgFabrik_Element::getFilterQuery()
	 */

	public function getFilterQuery($key, $condition, $value, $originalValue, $type = 'normal')
	{
		$originalValue = trim($value, "'");
		$this->encryptFieldName($key);
		$str = ' ('.$key.' '.$condition.' '.$value.' OR '.$key.' LIKE \'%"'.$originalValue.'"%\')';
		return $str;
	}

}
?>
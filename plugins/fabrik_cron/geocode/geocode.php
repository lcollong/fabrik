<?php

/**
* A cron task to email records to a give set of users
* @package Joomla
* @subpackage Fabrik
* @author Hugh Messenger
* @copyright (C) Hugh Messenger
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

//require the abstract plugin class
require_once(COM_FABRIK_FRONTEND . '/models/plugin-cron.php');


require_once(JPATH_SITE . '/plugins/fabrik_cron/geocode/libs/gmaps.php');

class plgFabrik_CronGeocode extends plgFabrik_Cron {

	/**
	 * do the plugin action
	 */

	function process(&$data, &$listModel)
	{
		$params = $this->getParams();

		// grab the table model and find table name and PK
		$table = $listModel->getTable();
		$table_name = $table->db_table_name;
		$primary_key = $table->db_primary_key;
		$primary_key_element = FabrikString::shortColName($table->db_primary_key);

		// for now, we have to read the table ourselves.  We can't rely on the $data passed to us
		// because it can be arbitrarily filtered according to who happened to hit the page when cron
		// needed to run.

		$mydata = array();
		$db = FabrikWorker::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from($table_name);
		$db->setQuery($query);
		$mydata[0] = $db->loadObjectList();

		// grab all the params, like GMaps key, field names to use, etc
		$geocode_gmap_key = $params->get('geocode_gmap_key');
		$geocode_is_empty = $params->get('geocode_is_empty');
		$geocode_zoom_level = $params->get('geocode_zoom_level', '4');
		$geocode_map_element_long = $params->get('geocode_map_element');
		$geocode_map_element = FabrikString::shortColName($geocode_map_element_long);
		$geocode_addr1_element_long = $params->get('geocode_addr1_element');
		$geocode_addr1_element = $geocode_addr1_element_long ? FabrikString::shortColName($geocode_addr1_element_long) : '';
		$geocode_addr2_element_long = $params->get('geocode_addr2_element');
		$geocode_addr2_element = $geocode_addr2_element_long ? FabrikString::shortColName($geocode_addr2_element_long) : '';
		$geocode_city_element_long = $params->get('geocode_city_element');
		$geocode_city_element = $geocode_city_element_long ? FabrikString::shortColName($geocode_city_element_long) : '';
		$geocode_state_element_long = $params->get('geocode_state_element');
		$geocode_state_element = $geocode_state_element_long ? FabrikString::shortColName($geocode_state_element_long) : '';
		$geocode_zip_element_long = $params->get('geocode_zip_element');
		$geocode_zip_element = $geocode_zip_element_long ? FabrikString::shortColName($geocode_zip_element_long) : '';
		$geocode_country_element_long = $params->get('geocode_country_userid_element');
		$geocode_country_element = $geocode_country_element_long ? FabrikString::shortColName($geocode_country_element_long) : '';

		// sanity check, make sure required elements have been specified
		if (empty($geocode_gmap_key))
		{
			JError::raiseNotice(500, 'No google maps key specified');
			return;
		}
		$gmap = new GMaps($geocode_gmap_key);
		// run through our table data
		$total_encoded = 0;
		foreach ($mydata as $gkey => $group)
		{
			if (is_array($group))
			{
				foreach ($group as $rkey => $row)
				{
					// see if the map element is considered empty
					if (empty($row->$geocode_map_element) || $row->$geocode_map_element == $geocode_is_empty)
					{
						// it's empty, so lets try and geocode.
						// first, construct the address
						// we'll build an array of address components, which we'll explode into a string later
						$a_full_addr = array();
						// for each address component element, see if one is specific in the params,
						// if so, see if it has a value in this row
						// if so, add it to the address array.
						if ($geocode_addr1_element)
						{
							if ($row->$geocode_addr1_element)
							{
								$a_full_addr[] = $row->$geocode_addr1_element;
							}
						}
						if ($geocode_addr2_element)
						{
							if ($row->$geocode_addr2_element)
							{
								$a_full_addr[] = $row->$geocode_addr2_element;
							}
						}
						if ($geocode_city_element)
						{
							if ($row->$geocode_city_element)
							{
								$a_full_addr[] = $row->$geocode_city_element;
							}
						}
						if ($geocode_state_element)
						{
							if ($row->$geocode_state_element)
							{
								$a_full_addr[] = $row->$geocode_state_element;
							}
						}
						if ($geocode_zip_element)
						{
							if ($row->$geocode_zip_element)
							{
								$a_full_addr[] = $row->$geocode_zip_element;
							}
						}
						if ($geocode_country_element)
						{
							if ($row->$geocode_country_element)
							{
								$a_full_addr[] = $row->$geocode_zip_element;
							}
						}
						// now explode the address into a string
						$full_addr = implode(',', $a_full_addr);

						// Did we actually get an address?
						if (!empty($full_addr))
						{
							// OK!  Lets try and geocode it ...
							if ($gmap->getInfoLocation($full_addr))
							{
								$lat = $gmap->getLatitude();
								$long = $gmap->getLongitude();
								if (!empty($lat) && !empty($long))
								{
									$map_value = "($lat,$long):$geocode_zoom_level";
									$query = $db->getQuery(true);
									$query->update($table_name)->set($geocode_map_element . ' = ' . $db->quote($map_value))
									->where($primary_key . ' = ' . $db->quote($row->$primary_key_element));
									$db->setQuery($query);
									$db->query();
									$total_encoded ++;
								}
							}
							else
							{
								FabrikWorker::log('plg.cron.geocode.information', sprintf('no geocode result for: %s', $full_addr));
							}
						}
						else
						{
							FabrikWorker::log('plg.cron.geocode.information', 'empty address');
						}
					}
				}
			}
		}
		return $total_encoded;
	}
}
?>
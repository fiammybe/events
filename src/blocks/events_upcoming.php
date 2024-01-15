<?php

/**
 * Functions to edit and display the Upcoming Events block.
 *
 * @copyright	https://www.isengard.biz Isengard.biz
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @author		Madfish
 * @since		1.0
 * @package		Events
 * @version		$Id$
 */

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

/**
 * Prepares the Upcoming Events block for display
 *
 * @param array $options
 * @return string
 */


function events_upcoming_show($options) : array
{
	global $icmsConfig, $eventsConfig;
	
	$eventsModule = icms::handler("icms_module")->getByDirname("events");
	include_once(ICMS_ROOT_PATH . '/modules/' . $eventsModule->getVar('dirname') . '/include/common.php');
	$events_event_handler = icms_getModuleHandler('event', 'events', 'events');
	
	// Check for dynamic tag filtering, including by untagged content
	$untagged_content = FALSE;
	if ($options[3] == 1 && isset($_GET['tag_id'])) {
		if ($_GET['tag_id'] == 'untagged') {
			$untagged_content = TRUE;
		}
		$options[2] = (int)trim($_GET['tag_id']);
	}
	
	// Retrieve the next XX events, optionally filtered by tag
	if (icms_get_module_status("sprockets") && ($options[2] != 0 || $untagged_content)) {
		$sprockets_taglink_handler = icms_getModuleHandler('taglink', 'sprockets', 'sprockets');
		$query = "SELECT * FROM " . $events_event_handler->table . ", "
			. $sprockets_taglink_handler->table
			. " WHERE `event_id` = `iid`";
		if ($untagged_content) {
			$options[2] = 0;
		}
		$query .= " AND `tid` = '" . $options[2] . "'"
			. " AND `mid` = '" . $eventsModule->getVar('mid') . "'"
			. " AND `item` = 'event'"
			. " AND `end_date` > '" . time() . "'"
			. " AND `online_status` = '1'";		
		$query .= " ORDER BY `date` ASC";
		$result = icms::$xoopsDB->query($query);
		if (!$result)
		{
			echo 'Error: Events block';
			exit;
		}
		else
		{
			$rows = $events_event_handler->convertResultSet($result, TRUE, TRUE);
			foreach ($rows as $key => $row) 
			{
				$block['upcoming_events'][$key] = $row;
			}
		}
	} else {
		// Do not filter by tag
		$criteria = new icms_db_criteria_Compo();
		$criteria->setStart(0);
		$criteria->setLimit($options[0]);
		$criteria->setSort('date');
		$criteria->setOrder('ASC');
		$criteria->add(new icms_db_criteria_Item('online_status', TRUE));
		$criteria->add(new icms_db_criteria_Item('end_date', time(), '>'));
		$block['upcoming_events'] = $events_event_handler->getObjects($criteria, TRUE, TRUE);
	}
	
	// Check that some results have been returned, otherwise stop processing
	if (empty($block['upcoming_events'])) {
		$block = array();
		return $block;
	}

	// Prepare event links for display
	foreach ($block['upcoming_events'] as &$event)
	{
		$title = $event->getVar('title');
		// $itemLink = $event->getItemLinkWithSEOString();
		// Trim the title if its length exceeds the block preferences
		if (strlen($title) > $options[1])
		{
			$event->setVar('title', substr($title, 0, ($options[1] - 3)) . '...');
		}
		
		// Adjust the itemLink according to whether there is a description or not
		$description = $event->getVar('description', 'e');
		$identifier = $event->getVar('identifier', 'e');

		// Formats timestamp according to the block options
		$date = $event->getVar('date', 'e');
		$dateformat = icms_getConfig('date_format', 'events');
		$date = date($dateformat, $date);
		
		// Convert to array for template insertion and update fields where required
		$event = $event->toArray();
		$event['date'] = $date;
		if (empty($description) && !empty($identifier))
		{
			$event['itemLink'] = '<a href="' . $identifier . '">' . $event['title'] . '</a>';
		}
	}
	
	return $block;
}

/**
 * Prepares the Upcoming Events block for editing
 * 
 * @param array ($options
 * @return string
 */

function events_upcoming_edit($options)
{
	$eventsModule = icms_getModuleInfo('events');
	include_once(ICMS_ROOT_PATH . '/modules/' . $eventsModule->getVar('dirname') . '/include/common.php');
	$events_event_handler = icms_getModuleHandler('event', 'events', 'events');

	// Select number of upcoming events to display in the block
	$form = '<table><tr>';
	$form .= '<tr><td>' . _MB_EVENTS_EVENT_UPCOMING_LIMIT . '</td>';
	$form .= '<td>' . '<input type="text" name="options[]" value="' . $options[0] . '" /></td>';
	$form .= '</tr>';
	
	// Limit title length
	$form .= '<tr><td>' . _MB_EVENTS_EVENT_TITLE_LENGTH . '</td>';
	$form .= '<td>' . '<input type="text" name="options[1]" value="' . $options[1]
		. '" /></td></tr>';

	if (icms_get_module_status("sprockets"))
	{
		// Optionally display results from a single tag - but only if sprockets module is installed
		$sprocketsModule = icms::handler("icms_module")->getByDirname("sprockets");
		$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->getVar('dirname'), 'sprockets');
		$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->getVar('dirname'), 'sprockets');
		
		// Get only those tags that contain content from this module
		$criteria = '';
		$relevant_tag_ids = array();
		$criteria = icms_buildCriteria(array('mid' => $eventsModule->getVar('mid')));
		$events_module_taglinks = $sprockets_taglink_handler->getObjects($criteria, TRUE, TRUE);
		foreach ($events_module_taglinks as $key => $value)
		{
			$relevant_tag_ids[] = $value->getVar('tid');
		}
		$relevant_tag_ids = array_unique($relevant_tag_ids);
		$relevant_tag_ids = '(' . implode(',', $relevant_tag_ids) . ')';
		unset($criteria);

		$criteria = new icms_db_criteria_Compo();
		$criteria->add(new icms_db_criteria_Item('tag_id', $relevant_tag_ids, 'IN'));
		$criteria->add(new icms_db_criteria_Item('label_type', '0'));
		$tagList = $sprockets_tag_handler->getList($criteria);

		$tagList = array(0 => _MB_EVENTS_ALL_TAGS) + $tagList;
		$form .= '<tr><td>' . _MB_EVENTS_FILTER_BY_TAG . '</td>';
		// Parameters icms_form_elements_Select: ($caption, $name, $value = null, $size = 1, $multiple = TRUE)
		$form_select = new icms_form_elements_Select('', 'options[2]', $options[2], '1', FALSE);
		$form_select->addOptionArray($tagList);
		$form .= '<td>' . $form_select->render() . '</td></tr>';
		
		// Dynamic tagging (overrides static tag filter)
		$form .= '<tr><td>' . _MB_EVENTS_DYNAMIC_TAG . '</td>';			
		$form .= '<td><input type="radio" name="options[3]" value="1"';
		if ($options[3] == 1) {
			$form .= ' checked="checked"';
		}
		$form .= '/>' . _MB_EVENTS_EVENT_YES;
		$form .= '<input type="radio" name="options[3]" value="0"';
		if ($options[3] == 0) {
			$form .= 'checked="checked"';
		}
			$form .= '/>' . _MB_EVENTS_EVENT_NO . '</td></tr>';
		}
	
	$form .= '</table>';

	return $form;
}function events_upcoming_menu_show($options)
{
	global $icmsConfig, $eventsConfig;

	$eventsModule = icms::handler("icms_module")->getByDirname("events");
	include_once(ICMS_ROOT_PATH . '/modules/' . $eventsModule->getVar('dirname') . '/include/common.php');
	$events_event_handler = icms_getModuleHandler('event', 'events', 'events');

	// Check for dynamic tag filtering, including by untagged content
	$untagged_content = FALSE;
	if ($options[3] == 1 && isset($_GET['tag_id'])) {
		if ($_GET['tag_id'] == 'untagged') {
			$untagged_content = TRUE;
		}
		$options[2] = (int)trim($_GET['tag_id']);
	}

	// Retrieve the next XX events, optionally filtered by tag
	if (icms_get_module_status("sprockets") && ($options[2] != 0 || $untagged_content)) {
		$sprockets_taglink_handler = icms_getModuleHandler('taglink', 'sprockets', 'sprockets');
		$query = "SELECT * FROM " . $events_event_handler->table . ", "
			. $sprockets_taglink_handler->table
			. " WHERE `event_id` = `iid`";
		if ($untagged_content) {
			$options[2] = 0;
		}
		$query .= " AND `tid` = '" . $options[2] . "'"
			. " AND `mid` = '" . $eventsModule->getVar('mid') . "'"
			. " AND `item` = 'event'"
			. " AND `end_date` > '" . time() . "'"
			. " AND `online_status` = '1'";
		$query .= " ORDER BY `date` ASC";
		$result = icms::$xoopsDB->query($query);
		if (!$result)
		{
			echo 'Error: Events block';
			exit;
		}
		else
		{
			$rows = $events_event_handler->convertResultSet($result, TRUE, TRUE);
			foreach ($rows as $key => $row)
			{
				$block['upcoming_events'][$key] = $row;
			}
		}
	} else {
		// Do not filter by tag
		$criteria = new icms_db_criteria_Compo();
		$criteria->setStart(0);
		$criteria->setLimit($options[0]);
		$criteria->setSort('date');
		$criteria->setOrder('ASC');
		$criteria->add(new icms_db_criteria_Item('online_status', TRUE));
		$criteria->add(new icms_db_criteria_Item('end_date', time(), '>'));
		$block['upcoming_events'] = $events_event_handler->getObjects($criteria, TRUE, TRUE);
	}

	// Check that some results have been returned, otherwise stop processing
	if (empty($block['upcoming_events'])) {
		$block = array();
		return $block;
	}

	// Prepare event links for display
	foreach ($block['upcoming_events'] as &$event)
	{
		$title = $event->getVar('title');
		// $itemLink = $event->getItemLinkWithSEOString();
		// Trim the title if its length exceeds the block preferences
		if (strlen($title) > $options[1])
		{
			$event->setVar('title', substr($title, 0, ($options[1] - 3)) . '...');
		}

		// Adjust the itemLink according to whether there is a description or not
		$description = $event->getVar('description', 'e');
		$identifier = $event->getVar('identifier', 'e');

		// Formats timestamp according to the block options
		$date = $event->getVar('date', 'e');
		$dateformat = icms_getConfig('date_format', 'events');
		$date = date($dateformat, $date);

		// Convert to array for template insertion and update fields where required
		$event = $event->toArray();
		$event['date'] = $date;
		if (empty($description) && !empty($identifier))
		{
			$event['itemLink'] = '<a href="' . $identifier . '">' . $event['title'] . '</a>';
		}
	}

	return $block;
}

/**
 * Prepares the Upcoming Events block for editing
 *
 * @param array ($options
 * @return string
 */

function events_upcoming_menu_edit($options)
{
	$eventsModule = icms_getModuleInfo('events');
	include_once(ICMS_ROOT_PATH . '/modules/' . $eventsModule->getVar('dirname') . '/include/common.php');
	$events_event_handler = icms_getModuleHandler('event', 'events', 'events');

	// Select number of upcoming events to display in the block
	$form = '<table><tr>';
	$form .= '<tr><td>' . _MB_EVENTS_EVENT_UPCOMING_LIMIT . '</td>';
	$form .= '<td>' . '<input type="text" name="options[]" value="' . $options[0] . '" /></td>';
	$form .= '</tr>';

	// Limit title length
	$form .= '<tr><td>' . _MB_EVENTS_EVENT_TITLE_LENGTH . '</td>';
	$form .= '<td>' . '<input type="text" name="options[1]" value="' . $options[1]
		. '" /></td></tr>';

	if (icms_get_module_status("sprockets"))
	{
		// Optionally display results from a single tag - but only if sprockets module is installed
		$sprocketsModule = icms::handler("icms_module")->getByDirname("sprockets");
		$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->getVar('dirname'), 'sprockets');
		$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->getVar('dirname'), 'sprockets');

		// Get only those tags that contain content from this module
		$criteria = '';
		$relevant_tag_ids = array();
		$criteria = icms_buildCriteria(array('mid' => $eventsModule->getVar('mid')));
		$events_module_taglinks = $sprockets_taglink_handler->getObjects($criteria, TRUE, TRUE);
		foreach ($events_module_taglinks as $key => $value)
		{
			$relevant_tag_ids[] = $value->getVar('tid');
		}
		$relevant_tag_ids = array_unique($relevant_tag_ids);
		$relevant_tag_ids = '(' . implode(',', $relevant_tag_ids) . ')';
		unset($criteria);

		$criteria = new icms_db_criteria_Compo();
		$criteria->add(new icms_db_criteria_Item('tag_id', $relevant_tag_ids, 'IN'));
		$criteria->add(new icms_db_criteria_Item('label_type', '0'));
		$tagList = $sprockets_tag_handler->getList($criteria);

		$tagList = array(0 => _MB_EVENTS_ALL_TAGS) + $tagList;
		$form .= '<tr><td>' . _MB_EVENTS_FILTER_BY_TAG . '</td>';
		// Parameters icms_form_elements_Select: ($caption, $name, $value = null, $size = 1, $multiple = TRUE)
		$form_select = new icms_form_elements_Select('', 'options[2]', $options[2], '1', FALSE);
		$form_select->addOptionArray($tagList);
		$form .= '<td>' . $form_select->render() . '</td></tr>';

		// Dynamic tagging (overrides static tag filter)
		$form .= '<tr><td>' . _MB_EVENTS_DYNAMIC_TAG . '</td>';
		$form .= '<td><input type="radio" name="options[3]" value="1"';
		if ($options[3] == 1) {
			$form .= ' checked="checked"';
		}
		$form .= '/>' . _MB_EVENTS_EVENT_YES;
		$form .= '<input type="radio" name="options[3]" value="0"';
		if ($options[3] == 0) {
			$form .= 'checked="checked"';
		}
			$form .= '/>' . _MB_EVENTS_EVENT_NO . '</td></tr>';
		}

	$form .= '</table>';

	return $form;
}

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

function events_upcoming_show($options)
{
	global $icmsConfig, $eventsConfig;
	
	$eventsModule = icms::handler("icms_module")->getByDirname("events");
	include_once(ICMS_ROOT_PATH . '/modules/' . $eventsModule->getVar('dirname') . '/include/common.php');
	$events_event_handler = icms_getModuleHandler('event', $eventsModule->getVar('dirname'), 'events');
	
	// Retrieve the next XX events
	$criteria = new icms_db_criteria_Compo();
	$criteria->setStart(0);
	$criteria->setLimit($options[0]);
	$criteria->setSort('date');
	$criteria->setOrder('DESC');
	$criteria->add(new icms_db_criteria_Item('online_status', TRUE));
	$criteria->add(new icms_db_criteria_Item('date', time(), '>'));

	// Retrieve the events to show in the block
	$block['upcoming_events'] = $events_event_handler->getObjects($criteria, TRUE, TRUE);

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

		// Add the SEO string to the itemLink
		// $event['itemLink'] = $itemLink;
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
	$events_event_handler = icms_getModuleHandler('event', $eventsModule->getVar('dirname'), 'events');

	// Select number of upcoming events to display in the block
	$form = '<table><tr>';
	$form .= '<tr><td>' . _MB_EVENTS_EVENT_UPCOMING_LIMIT . '</td>';
	$form .= '<td>' . '<input type="text" name="options[]" value="' . $options[0] . '" /></td>';
	$form .= '</tr>';
	
	// Limit title length
	$form .= '<tr><td>' . _MB_EVENTS_EVENT_TITLE_LENGTH . '</td>';
	$form .= '<td>' . '<input type="text" name="options[3]" value="' . $options[1]
		. '" /></td></tr>';
	$form .= '</table>';

	return $form;
}

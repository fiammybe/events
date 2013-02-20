<?php
/**
* Event page
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2013.
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		events
* @version		$Id$
*/

include_once "header.php";

$xoopsOption["template_main"] = "events_event.html";
include_once ICMS_ROOT_PATH . "/header.php";

$events_event_handler = icms_getModuleHandler("event", basename(dirname(__FILE__)), "events");

$clean_event_id = isset($_GET["event_id"]) ? (int)$_GET["event_id"] : 0 ;
$eventObj = $events_event_handler->get($clean_event_id);

if($eventObj && !$eventObj->isNew()) {
	$icmsTpl->assign("events_event", $events_event_handler->prepareEventForDisplay($eventObj, TRUE));

	$icms_metagen = new icms_ipf_Metagen($eventObj->getVar("title"), $eventObj->getVar("meta_keywords", "n"), $eventObj->getVar("meta_description", "n"));
	$icms_metagen->createMetaTags();
} else {
	////////////////////////////////////////////////////////////////////////
	////////// Display event index page, sorted by year and month //////////
	////////////////////////////////////////////////////////////////////////
	
	// Get the current date/time; this is used to filter out expired events
	$time = time();
	$criteria = new icms_db_criteria_Compo();
	$criteria->add(new icms_db_criteria_Item('date', $time, '>'));
	$criteria->add(new icms_db_criteria_Item('online_status', '1'));
	$criteria->setSort('date');
	$criteria->setOrder('ASC');
	$events_event_array = $events_event_handler->getObjects($criteria, TRUE, TRUE);
	
	///////////////////////////////////////////////////////
	////////// Sort the events by year and month //////////
	///////////////////////////////////////////////////////
	
	$sorted_events = array();
	
	foreach ($events_event_array as $eventObj)
	{
		$event = '';
		$year = date('Y', $eventObj->getVar('date', 'e'));
		$month = date('F', $eventObj->getVar('date', 'e'));
		
		// Format the start/end dates for user-side display
		$event = $events_event_handler->prepareEventForDisplay($eventObj, FALSE);
		$sorted_events[$year][$month][] = $event;
	}
	
	// Assign data to template
	$icmsTpl->assign("events_title", _MD_EVENTS_ALL_EVENTS);
	$icmsTpl->assign("events_list", $sorted_events);
}

$icmsTpl->assign("events_page_title", icms::$module->getVar("name"));
$icmsTpl->assign("events_module_home", '<a href="' . ICMS_URL . "/modules/"
		. icms::$module->getVar("dirname") . '/">' . icms::$module->getVar("name") . "</a>");
$icmsTpl->assign("events_show_breadcrumb", icms::$module->config['show_breadcrumb']);

include_once "footer.php";
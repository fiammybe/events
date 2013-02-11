<?php
/**
 * Events version infomation
 *
 * This file holds the search function for the Events module
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2013.
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		events
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

function events_search($queryarray, $andor, $limit, $offset = 0, $userid)
{
	global $icmsConfigSearch;
	
	$eventsArray = $ret = array();
	$count = $number_to_process = $events_left = '';
	
	$events_event_handler = icms_getModuleHandler("event", basename(dirname(dirname(__FILE__))), "events");
	$eventsArray = $events_event_handler->getEventsForSearch($queryarray, $andor, $limit, $offset, $userid);
	
	// Count the number of records
	$count = count($eventsArray);
	
	// The number of records actually containing event objects is <= $limit, the rest are padding
	$events_left = ($count - ($offset + $icmsConfigSearch['search_per_page']));
	if ($events_left < 0) {
		$number_to_process = $icmsConfigSearch['search_per_page'] + $events_left; // $events_left is negative
	} else {
		$number_to_process = $icmsConfigSearch['search_per_page'];
	}
	
	// Process the actual events (not the padding)
	for ($i = 0; $i < $number_to_process; $i++) {
		$item['image'] = "images/event.png";
		$item['link'] = $eventsArray[$i]->getItemLink(TRUE);
		$item['title'] = $eventsArray[$i]->getVar("title");
		$item['time'] = $eventsArray[$i]->getVar("date", "e");
		$item['uid'] = $eventsArray[$i]->getVar("creator");
		$ret[] = $item;
		unset($item);
	}
	
	// Restore the padding (required for 'hits' information and pagination controls). The offset
	// must be padded to the left of the results, and the remainder to the right or else the search
	// pagination controls will display the wrong results (which will all be empty).
	// Left padding = -($limit + $offset)
	$ret = array_pad($ret, -($offset + $number_to_process), 1);
	
	// Right padding = $count
	$ret = array_pad($ret, $count, 1);
	
	return $ret;
}
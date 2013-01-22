<?php
/**
 * Events version infomation
 *
 * This file holds the configuration information of this module
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2013.
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		events
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

function events_search($queryarray, $andor, $limit, $offset, $userid)
{

	$events_event_handler = icms_getModuleHandler("event", basename(dirname(dirname(__FILE__))), "events");
	$eventsArray = $events_event_handler->getEventsForSearch($queryarray, $andor, $limit, $offset, $userid);

	$ret = array();

	foreach ($eventsArray as $event) {
		$item['image'] = "images/event.png";
		$item['link'] = $event->getItemLink(TRUE);
		$item['title'] = $event->getVar("title");
		$item['time'] = $event->getVar("date", "e");
		$item['uid'] = $event->getVar("creator");
		$ret[] = $item;
		unset($item);
	}

	return $ret;
}
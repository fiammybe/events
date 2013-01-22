<?php
/**
 * Classes responsible for managing Events event objects
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2013.
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		events
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

class mod_events_EventHandler extends icms_ipf_Handler {
	/**
	 * Constructor
	 *
	 * @param icms_db_legacy_Database $db database connection object
	 */
	public function __construct(&$db) {
		parent::__construct($db, "event", "event_id", "title", "description", "events");

	}

	/**
	 * Formats an event objects data for insertion to display-side templates
	 */
	public function prepareEventForDisplay($eventObj, $single_view = FALSE)
	{
		// Initialise variables
		$event = $eventObj->toArray();
		$start_date = $eventObj->getVar('date', 'e');
		$end_date = $eventObj->getVar('end_date', 'e');
			
		// Format date. Check the month of each. Are they the same?
		if (date('n', $start_date) == date('n', $end_date))
		{
			// If so, format using the start month once, eg. 1-3 January
			$event['formatted_date'] = date('j', $start_date) . '-' . date ('j', $end_date) . ' ' 
					. date('F', $start_date);
		}
		else
		{
			// If not, then specify using both the start/end months
			$event['formatted_date'] = date('j', $start_date) . ' ' . date('F', $start_date) . ' - '
					. date ('j', $end_date) . ' ' . date('F', $end_date);
		}
		
		// TEMPORARY: Set a 'link' field, which will be either i) the itemUrl, if the event has a
		// description, or ii) a direct link to an external website, if provided. This allows the
		// logic to be removed from the template, as there is currently a problem where HTML 
		// comments are inserted into empty fields, which means they do not evaluate as FALSE 
		// anymore.
		
		$title = $eventObj->getVar('title', 'e');
		$identifier = $eventObj->getVar('identifier', 'e');
		$short_url = $eventObj->getVar('short_url', 'e');
		
		if (!$single_view)
		{
			// View event as part of the index page list. Events with descriptions should be linked 
			// to single page view, events that don't have a description should link directly to the
			// official site, if available.
			$description = $eventObj->getVar('description', 'e');
			if (!empty($description))
			{
				$event['title'] = '<a href="' . $event['itemUrl'];
				if (!empty($short_url))
				{
					$event['title'] .= '&amp;title=' . $short_url; 
				}						
				$event['title'] .= '">' . $title . ', ' . $event['coverage'] . ', '
				. $event['formatted_date'] .'</a>';
			}
			else
			{
				if (!empty($identifier))
				{
					$event['title'] = '<a href="' . $event['identifier'] . '">' 
							. $event['title'] . ', ' . $event['coverage'] . ', '
							. $event['formatted_date'] .'</a>';
				}
			}
		}
		else
		{
			// View event in single item (details) mode. Title should link out to official event 
			// site if available, otherwise its plain text.
			if (!empty($identifier))
			{
				$event['title'] = '<a href="' . $event['identifier'] . '">' 
							. $event['title'] . '</a>';
			}
		}
		
		return $event;		
	}
	
	/**
	 * Provides the global search functionality for the Library module
	 *
	 * @param array $queryarray
	 * @param string $andor
	 * @param int $limit
	 * @param int $offset
	 * @param int $userid
	 * @return array 
	 */
	public function getEventsForSearch($queryarray, $andor, $limit, $offset, $userid)
	{		
		$criteria = new icms_db_criteria_Compo();
		$criteria->setStart($offset);
		$criteria->setLimit($limit);
		$criteria->setSort('date');
		$criteria->setOrder('DESC');

		if ($userid != 0) 
		{
			$criteria->add(new icms_db_criteria_Item('submitter', $userid));
		}
		
		if ($queryarray) 
		{
			$criteriaKeywords = new icms_db_criteria_Compo();
			for ($i = 0; $i < count($queryarray); $i++) {
				$criteriaKeyword = new icms_db_criteria_Compo();
				$criteriaKeyword->add(new icms_db_criteria_Item('title', '%' . $queryarray[$i] . '%',
					'LIKE'), 'OR');
				$criteriaKeyword->add(new icms_db_criteria_Item('description', '%' . $queryarray[$i]
					. '%', 'LIKE'), 'OR');
				$criteriaKeywords->add($criteriaKeyword, $andor);
				unset ($criteriaKeyword);
			}
			$criteria->add($criteriaKeywords);
		}
		
		$criteria->add(new icms_db_criteria_Item('online_status', TRUE));
		
		return $this->getObjects($criteria, TRUE, TRUE);
	}
	
	/**
	 * Toggles the event online_status field
	 *
	 * @param int $event_id
	 * @param str $field
	 * @return int $visibility
	 */
	public function changeStatus($id, $field) {
		
		$visibility = $eventObj = '';
		
		$eventObj = $this->get($id);
		if ($eventObj->getVar($field, 'e') == 1) {
			$eventObj->setVar($field, 0);
			$visibility = 0;
		} else {
			$eventObj->setVar($field, 1);
			$visibility = 1;
		}
		$this->insert($eventObj, TRUE);
		
		return $visibility;
	}
	
	/////////////////////////////////////////
	////////// ADMIN TABLE FILTERS //////////
	/////////////////////////////////////////
		
	/**
	 * Allows the events admin table to be sorted events marked as online/offline
	 */
	public function online_status_filter() {
		return array(0 =>  _CO_EVENTS_EVENT_OFFLINE, 1 =>  _CO_EVENTS_EVENT_ONLINE);
	}
	
	/**
	 * Validate data before saving or updating
	 * @param object $obj 
	 */
	protected function beforeSave(& $obj)
	{		
		// Check start date is before end date
		$start = $obj->getVar('date', 'e');
		$end = $obj->getVar('end_date', 'e');
		if ($start > $end) {
			// Complain
			$obj->setErrors(_CO_EVENTS_ERROR_BAD_DATE);
			return FALSE;
		}
		
		// Check URL is valid format
		$url = $obj->getVar('identifier', 'e');
		if ($url) {
			if (!filter_var($url, FILTER_VALIDATE_URL)) {
				// Complain
				$obj->setErrors(_CO_EVENTS_ERROR_BAD_URL);
				return FALSE;
			}
		}
		
		return TRUE;
	}
}
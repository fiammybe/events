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
	public function getEventsForSearch($queryarray, $andor, $limit, $offset = 0, $userid)
	{
		$count = $results = '';
		$criteria = new icms_db_criteria_Compo();

		if ($userid != 0) 
		{
			$criteria->add(new icms_db_criteria_Item('creator', $userid));
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
		
		/*
		 * Improving the efficiency of search
		 * 
		 * The general search function is not efficient, because it retrieves all matching records
		 * even when only a small subset is required, which is usually the case. The full records 
		 * are retrieved so that they can be counted, which is used to display the number of 
		 * search results and also to set up the pagination controls. The problem with this approach 
		 * is that a search generating a very large number of results (eg. > 650) will crash out. 
		 * Maybe its a memory allocation issue, I don't know.
		 * 
		 * A better approach is to run two queries: The first a getCount() to find out how many 
		 * records there are in total (without actually wasting resources to retrieve them), 
		 * followed by a getObjects() to retrieve the small subset that are actually needed. 
		 * Due to the way search works, the object array needs to be padded out 
		 * with the number of elements counted in order to preserve 'hits' information and to construct
		 * the pagination controls. So to minimise resources, we can just set their values to '1'.
		 * 
		 * In the long term it would be better to (say) pass the count back as element[0] of the 
		 * results array, but that will require modification to the core and will affect all modules.
		 * So for the moment, this hack is convenient.
		 */
		
		// Count the number of search results WITHOUT actually retrieving the objects
		$count = $this->getCount($criteria);
		
		$criteria->setStart($offset);
		$criteria->setSort('date');
		$criteria->setOrder('DESC');
		
		// Retrieve the subset of results that are actually required.
		// Problem: If show all results # < shallow search #, then the all results preference is 
		// used as a limit. This indicates that shallow search is not setting a limit! The largest 
		// of these two values should always be used
		if (!$limit) {
			global $icmsConfigSearch;
			$limit = $icmsConfigSearch['search_per_page'];
		}
		
		$criteria->setLimit($limit);
		$results = $this->getObjects($criteria, FALSE, TRUE);
		
		// Pad the results array out to the counted length to preserve 'hits' and pagination controls.
		// This approach is not ideal, but it greatly reduces the load for queries with large result sets
		$results = array_pad($results, $count, 1);
		
		return $results;
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
	 * Flush the cache for the Events module after adding, editing or deleting an event.
	 * 
	 * Ensures that the index/block/single view cache is kept updated if caching is enabled.
	 * 
	 * @global array $icmsConfig
	 * @param type $obj 
	 */
	protected function clear_cache(& $obj)
	{
		global $icmsConfig;
		$cache_status = $icmsConfig['module_cache'];
		$module = icms::handler("icms_module")->getByDirname("events", TRUE);
		$module_id = $module->getVar("mid");
			
		// Check if caching is enabled for this module. The cache time is stored in a serialised 
		// string in config table (module_cache), and is indicated in seconds. Uncached = 0.
		if ($cache_status[$module_id] > 0)
		{			
			// As PHP's exec() function is often disabled for security reasons
			try 
			{
				exec("find " . ICMS_CACHE_PATH . "/" . "events^%2Fmodules%2Fevents%2Fevent.php^* -delete &");
				exec("find " . ICMS_CACHE_PATH . "/" . "events^%2Fmodules%2Fevents%2Fcalendar.php^* -delete &");
				exec("find " . ICMS_CACHE_PATH . "/" . "blk_events* -delete &");
				if (!$obj->isNew())
				{
					exec("find " . ICMS_CACHE_PATH . "/" . "events^%2Fmodules%2Fevents%2Fevent.php%3Fevent_id%3D" 
							. $obj->getVar('event_id', 'e') . "%26* -delete &");
				}
				
			}
			catch(Exception $e)
			{
				$obj->setErrors($e->getMessage());
			}
		}		
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
	
	/**
	 * Activities that must be conducted after an object is saved.
	 * 
	 * @param Event object $obj
	 */
	
	protected function afterSave(& $obj) {		
		$this->clear_cache(& $obj);
		return TRUE;
	}
	
	/**
	 * Activities to be conducted after an event is deleted
	 * 
	 * @global array $icmsConfig
	 * @param Event object $obj
	 */
	protected function afterDelete(& $obj) {
		$this->clear_cache(& $obj);		
		return TRUE;
	}
}
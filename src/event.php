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

$events_event_handler = icms_getModuleHandler("event", "events", "events");
$events_event_array = array();

$clean_event_id = isset($_GET["event_id"]) ? (int)$_GET["event_id"] : 0 ;
$untagged_content = FALSE;
if (isset($_GET['tag_id'])) {
	if ($_GET['tag_id'] == 'untagged') {
		$untagged_content = TRUE;
	}
}

$clean_period = isset($_GET["period"]) ? $_GET["period"] : 0 ;

$clean_tag_id = isset($_GET['tag_id']) ? (int)trim($_GET['tag_id']) : 0;

// Optional tagging support (only if Sprockets module installed)
if (icms_get_module_status("sprockets"))
{
	$sprocketsModule = icms::handler("icms_module")->getByDirname("sprockets");
	icms_loadLanguageFile("sprockets", "common");
	$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->getVar('dirname'), 'sprockets');
	$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->getVar('dirname'), 'sprockets');
	$criteria = icms_buildCriteria(array('label_type' => '0'));
	$sprockets_tag_buffer = array(0 => _CO_EVENTS_ALL) + $sprockets_tag_handler->getList($criteria, TRUE, TRUE);
}

$eventObj = $events_event_handler->get($clean_event_id);

///////////////////////////////////////////////////////////////////////////////
////////// Display single event (only if it has a description field) //////////
///////////////////////////////////////////////////////////////////////////////
if ($eventObj && !$eventObj->isNew()) {

	// Prepare tags for display
	if (icms_get_module_status("sprockets"))
	{
		$event_tags = array();
		$event_tag_array = $sprockets_taglink_handler->getTagsForObject($eventObj->getVar('event_id'),
				$events_event_handler, 0);
		foreach ($event_tag_array as $key => $value)
		{
			$event_tags[] = '<a href="' . EVENTS_URL . 'event.php?tag_id=' . $value
					. '">' . $sprockets_tag_buffer[$value] . '</a>';
		}
		$event_tags = implode(', ', $event_tags);
		$eventObj->setVar('tag', $event_tags);
	}
	$icmsTpl->assign("events_event", $events_event_handler->prepareEventForDisplay($eventObj, TRUE));
	$icms_metagen = new icms_ipf_Metagen($eventObj->getVar("title"), $eventObj->getVar("meta_keywords", "n"), $eventObj->getVar("meta_description", "n"));
	$icms_metagen->createMetaTags();
    /**
     * OpenGraph tags for Facebook
     */
    $xoTheme->addMeta('meta','og:title',$eventObj->getVar('title'));
    $xoTheme->addMeta('meta','og:type','article');
    $xoTheme->addMeta('meta','og:description',$eventObj->getVar('description'));
    if ($eventObj->getVar('image')) {
        $xoTheme->addMeta('meta', 'og:image',ICMS_URL . '/uploads/' . basename(dirname(__FILE__, 1)) . '/article/' . $eventObj->getVar('image'));
    }
    /**
     * Twitter Cards tags
     */
    $xoTheme->addMeta('meta','twitter:card','summary');
    $xoTheme->addMeta('meta','twitter:title',$eventObj->getVar('title'));
    $xoTheme->addMeta('meta','twitter:description',$eventObj->getVar('description'));
    if ($eventObj->getVar('image')) {
        $xoTheme->addMeta('meta', 'twitter:image',ICMS_URL . '/uploads/' . basename(dirname(__FILE__, 1)) . '/article/' . $eventObj->getVar('image'));
    }
} else {
	////////////////////////////////////////////////////////////////////////
	////////// Display event index page, sorted by year and month //////////
	////////////////////////////////////////////////////////////////////////

	// Get the current date/time; this is used to filter out expired events
	$time = time();

	// Optional tagging support (only if Sprockets module installed)
	if (icms_get_module_status("sprockets"))
	{
		// Get a select box (if preferences allow, and only if Sprockets module installed)
		if (icms::$module->config['events_show_tag_select_box']) {
			if ($untagged_content) {
				$tag_select_box = $sprockets_tag_handler->getTagSelectBox('event.php', 'untagged',
					_CO_EVENTS_ALL_TAGS, TRUE, icms::$module->getVar('mid'), 'event', TRUE);
			} else {
				$tag_select_box = $sprockets_tag_handler->getTagSelectBox('event.php', $clean_tag_id,
					_CO_EVENTS_ALL_TAGS, TRUE, icms::$module->getVar('mid'), 'event', TRUE);
			}
			$icmsTpl->assign('events_tag_select_box', $tag_select_box);
		}

		// Append the tag name to the module title (if preferences allow, and only if Sprockets module installed)
		if (icms::$module->config['show_breadcrumb'] == FALSE)
		{
			if (array_key_exists($clean_tag_id, $sprockets_tag_buffer) && ($clean_tag_id !== 0))
			{
				$events_tag_name = $sprockets_tag_buffer[$clean_tag_id];
				$icmsTpl->assign('events_tag_name', $events_tag_name);
			} elseif ($untagged_content) {
				$events_tag_name = _CO_EVENTS_UNTAGGED;
				$icmsTpl->assign('events_tag_name', $events_tag_name);
			}
		}
		else
		{
			if ($untagged_content) {
				$icmsTpl->assign('events_category_path', _CO_EVENTS_UNTAGGED);
			} else {
				$icmsTpl->assign('events_category_path', $sprockets_tag_buffer[$clean_tag_id]);
			}
		}
	}

	// Get a list of events sorted by tag
	if (icms_get_module_status("sprockets") && ($clean_tag_id || $untagged_content))
	{
		$query = $rows = '';
		$linked_event_ids = array();

		$query = "SELECT * FROM " . $events_event_handler->table . ", "
				. $sprockets_taglink_handler->table
				. " WHERE `event_id` = `iid`"
				. " AND `online_status` = '1'"
				. " AND `tid` = '" . $clean_tag_id . "'"
				. " AND `mid` = '" . (int)icms::$module->getVar('mid') . "'"
				. " AND `date` > '" . $time . "'"
				. " AND `online_status` = '1'"
				. " AND `item` = 'event'"
				. " ORDER BY `date` ASC";
		$result = icms::$xoopsDB->query($query);
		if (!$result)
		{
			echo 'Error';
			exit;
		}
		else
		{
			$rows = $events_event_handler->convertResultSet($result, TRUE, TRUE);
			foreach ($rows as $key => $row)
			{
				$events_event_array[$key] = $row;
			}
		}
	}
	else
	{
		// Retrieve events without filtering by tag
		$criteria = new icms_db_criteria_Compo();
		$criteria->add(new icms_db_criteria_Item('date', $time, ($clean_period == 'past') ? '<' : '>';));
		$criteria->add(new icms_db_criteria_Item('online_status', '1'));
		$criteria->setSort('date');
		$criteria->setOrder('ASC');
		$events_event_array = $events_event_handler->getObjects($criteria, TRUE, TRUE);
	}

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

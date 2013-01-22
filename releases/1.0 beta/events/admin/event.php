<?php
/**
 * Admin page to manage events
 *
 * List, add, edit and delete event objects
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2013.
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		events
 * @version		$Id$
 */

/**
 * Edit a Event
 *
 * @param int $event_id Eventid to be edited
*/
function editevent($event_id = 0) {
	global $events_event_handler, $icmsModule, $icmsAdminTpl;

	$eventObj = $events_event_handler->get($event_id);

	if (!$eventObj->isNew()){
		$icmsModule->displayAdminMenu(0, _AM_EVENTS_EVENTS . " > " . _CO_ICMS_EDITING);
		$sform = $eventObj->getForm(_AM_EVENTS_EVENT_EDIT, "addevent");
		$sform->assign($icmsAdminTpl);
	} else {
		$icmsModule->displayAdminMenu(0, _AM_EVENTS_EVENTS . " > " . _CO_ICMS_CREATINGNEW);
		$sform = $eventObj->getForm(_AM_EVENTS_EVENT_CREATE, "addevent");
		$sform->assign($icmsAdminTpl);

	}
	$icmsAdminTpl->display("db:events_admin_event.html");
}

include_once "admin_header.php";

$events_event_handler = icms_getModuleHandler("event", basename(dirname(dirname(__FILE__))), "events");

/** Use a naming convention that indicates the source of the content of the variable */
$clean_op = "";

/** Create a whitelist of valid values, be sure to use appropriate types for each value
 * Be sure to include a value for no parameter, if you have a default condition
 */
$valid_op = array ("mod", "changedField", "addevent", "del", "view", "changeStatus", "");

if (isset($_GET["op"])) $clean_op = htmlentities($_GET["op"]);
if (isset($_POST["op"])) $clean_op = htmlentities($_POST["op"]);

/** Again, use a naming convention that indicates the source of the content of the variable */
$clean_event_id = isset($_GET["event_id"]) ? (int)$_GET["event_id"] : 0 ;

/**
 * in_array() is a native PHP function that will determine if the value of the
 * first argument is found in the array listed in the second argument. Strings
 * are case sensitive and the 3rd argument determines whether type matching is
 * required
*/
if (in_array($clean_op, $valid_op, TRUE)) {
	switch ($clean_op) {
		case "mod":
		case "changedField":
			icms_cp_header();
			editevent($clean_event_id);
			break;
		
		case "changeStatus":
			$status = $events_event_handler->changeStatus($clean_event_id, 'online_status');
			$ret = '/modules/' . basename(dirname(dirname(__FILE__))) . '/admin/event.php';
			if ($status == 0) {
				redirect_header(ICMS_URL . $ret, 2, _AM_EVENTS_EVENT_OFFLINE);
			} else {
				redirect_header(ICMS_URL . $ret, 2, _AM_EVENTS_EVENT_ONLINE);
			}
			break;

		case "addevent":	
			$controller = new icms_ipf_Controller($events_event_handler);
			$controller->storeFromDefaultForm(_AM_EVENTS_EVENT_CREATED, _AM_EVENTS_EVENT_MODIFIED);
			break;

		case "del":
			$controller = new icms_ipf_Controller($events_event_handler);
			$controller->handleObjectDeletion();
			break;

		case "view" :
			$eventObj = $events_event_handler->get($clean_event_id);
			icms_cp_header();
			$eventObj->displaySingleObject();
			break;

		default:
			icms_cp_header();
			$icmsModule->displayAdminMenu(0, _AM_EVENTS_EVENTS);
			$objectTable = new icms_ipf_view_Table($events_event_handler);
			$objectTable->addColumn(new icms_ipf_view_Column("online_status"));
			$objectTable->addColumn(new icms_ipf_view_Column("title"));
			$objectTable->addColumn(new icms_ipf_view_Column("coverage"));
			$objectTable->addColumn(new icms_ipf_view_Column("date"));
			$objectTable->addColumn(new icms_ipf_view_Column("end_date"));
			$objectTable->setDefaultSort('date');
			$objectTable->setDefaultOrder('DESC');
			$objectTable->addFilter('online_status', 'online_status_filter');
			$objectTable->addIntroButton("addevent", "event.php?op=mod", _AM_EVENTS_EVENT_CREATE);
			$icmsAdminTpl->assign("events_event_table", $objectTable->fetch());
			$icmsAdminTpl->display("db:events_admin_event.html");
			break;
	}
	icms_cp_footer();
}
/**
 * If you want to have a specific action taken because the user input was invalid,
 * place it at this point. Otherwise, a blank page will be displayed
 */
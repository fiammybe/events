<?php
/**
 * English language constants related to module information
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2013.
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		events
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

define("_MI_EVENTS_MD_NAME", "Events");
define("_MI_EVENTS_MD_DESC", "ImpressCMS Simple Events");
define("_MI_EVENTS_EVENTS", "Events");
define("_MI_EVENTS_SHOW_BREADCRUMB", "Show breadcrumb?");
define("_MI_EVENTS_SHOW_BREADCRUMB_DSC", "Toggles the breadcrumb navigation strip on and off.");
define("_MI_EVENTS_TEMPLATES", "Templates");
define("_MI_EVENTS_DATE_FORMAT", "Date format");
define("_MI_EVENTS_DATE_FORMAT_DSC", "Specify the date format (see the date() function in the PHP manual for modifier codes).");

// Blocks
define("_MI_EVENTS_UPCOMING", "Upcoming events");
define("_MI_EVENTS_UPCOMINGDSC", "Displays forthcoming events");
// Since 2.0
define("_MI_EVENTS_UPCOMING_MENU", "Upcoming events in menu");
define("_MI_EVENTS_UPCOMING_MENUDSC", "Displays forthcoming events in menu");


// Added in V1.01
define("_MI_EVENTS_CALENDAR", "Calendar");
define("_MI_EVENTS_LIST", "List");
define("_MI_EVENTS_START_PAGE", "Select module start page");
define("_MI_EVENTS_START_PAGE_DSC", "To display a simple event listing by default, select 'event.php', to select a calendar-style display select calendar.php");
define("_MI_EVENTS_SHOW_TAG_SELECT_BOX", "Display tag select box?");
define("_MI_EVENTS_SHOW_TAG_SELECT_BOX_DSC", "Allows module content to be filtered by tag. Only if Sprockets module is installed");
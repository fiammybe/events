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

/**  General Information  */
$modversion = array(
	"name"						=> _MI_EVENTS_MD_NAME,
	"version"					=> 1.02,
	"description"				=> _MI_EVENTS_MD_DESC,
	"author"					=> "Madfish (Simon Wilkinson)",
	"credits"					=> "Thanks to Will for adding the calendar page/functionality. Logo by lopagof (Creative Commons Attribution Non-Commercial): http://lopagof.deviantart.com/",
	"help"						=> "",
	"license"					=> "GNU General Public License (GPL)",
	"official"					=> 0,
	"dirname"					=> basename(dirname(__FILE__)),
	"modname"					=> "events",

/**  Images information  */
	"iconsmall"					=> "images/icon_small.png",
	"iconbig"					=> "images/icon_big.png",
	"image"						=> "images/icon_big.png", /* for backward compatibility */

/**  Development information */
	"status_version"			=> "1.02",
	"status"					=> "BETA",
	"date"						=> "2/3/2015",
	"author_word"				=> "",
	"warning"					=> "",

/** Contributors */
	"developer_website_url"		=> "https://www.isengard.biz",
	"developer_website_name"	=> "Isengard.biz",
	"developer_email"			=> "simon@isengard.biz",

/** Administrative information */
	"hasAdmin"					=> 1,
	"adminindex"				=> "admin/index.php",
	"adminmenu"					=> "admin/menu.php",

/** Install and update informations */
	"onInstall"					=> "include/onupdate.inc.php",
	"onUpdate"					=> "include/onupdate.inc.php",

/** Search information */
	"hasSearch"					=> 1,
	"search"					=> array("file" => "include/search.inc.php", "func" => "events_search"));

/** Menu information */
	$i = 0;
	$modversion["hasMain"]		= 1;
	if (icms_getConfig('events_start_page', 'events') == 0) {
		$modversion['sub'][$i]['name'] = _MI_EVENTS_CALENDAR;
		$modversion['sub'][$i]['url'] = "calendar.php";
	} else {
		$modversion['sub'][$i]['name'] = _MI_EVENTS_LIST;
		$modversion['sub'][$i]['url'] = "event.php";
	}				

/** Comments information */
	$modversion["hasComments"]	= 0;

/** other possible types: testers, translators, documenters and other */
$modversion['people']['developers'][] = "Madfish (Simon Wilkinson)";

/** Database information */
$modversion['object_items'][1] = 'event';

$modversion["tables"] = icms_getTablesArray($modversion['dirname'], $modversion['object_items']);

/** Templates information */
$modversion['templates'] = array(
	array("file" => "events_admin_event.html", "description" => "Event admin index"),
	array("file" => "events_event.html", "description" => "Event index"),
	array("file" => "events_calendar.html", "description" => "Event Calendar"),
	array("file" => "events_requirements.html", "description" => "Event requirements"),
	array("file" => "events_header.html", "description" => "Module header"),
	array("file" => "events_footer.html", "description" => "Module footer"));

/** Blocks information */
$modversion['blocks'][1] = array(
  'file' => 'events_upcoming.php',
  'name' => _MI_EVENTS_UPCOMING,
  'description' => _MI_EVENTS_UPCOMINGDSC,
  'show_func' => 'events_upcoming_show',
  'edit_func' => 'events_upcoming_edit',
  'options' => '5|90|0',
  'template' => 'events_upcoming.html');

/** Preferences information */

$start_options = array(0 => 'event.php', 1 => 'calendar.php');
$start_options = array_flip($start_options);

// Module start page
$modversion['config'][3] = array(
	'name' => 'events_start_page',
	'title' => '_MI_EVENTS_START_PAGE',
	'description' => '_MI_EVENTS_START_PAGE_DSC',
	'formtype' => 'select',
	'valuetype' => 'text',
	'options' => $start_options,
	'default' =>  '0');

// Display breadcrumb
$modversion['config'][] = array(
	'name' => 'show_breadcrumb',
	'title' => '_MI_EVENTS_SHOW_BREADCRUMB',
	'description' => '_MI_EVENTS_SHOW_BREADCRUMB_DSC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => '1');

$modversion['config'][] = array(
	'name' => 'events_show_tag_select_box',
	'title' => '_MI_EVENTS_SHOW_TAG_SELECT_BOX',
	'description' => '_MI_EVENTS_SHOW_TAG_SELECT_BOX_DSC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => '0'
);

// Format the date field (see PHP manual on date() function for format modifiers)
$modversion['config'][] = array(
	'name' => 'date_format',
	'title' => '_MI_EVENTS_DATE_FORMAT',
	'description' => '_MI_EVENTS_DATE_FORMAT_DSC',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'j/n/Y'); // Changing these modifiers changes the date format

// Comments information
$modversion['hasComments'] = 0;

/** Notification information */
/** To come soon in imBuilding... */
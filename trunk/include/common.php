<?php
/**
 * Common file of the module included on all pages of the module
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2013.
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		events
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

if (!defined("EVENTS_DIRNAME")) define("EVENTS_DIRNAME", $modversion["dirname"] = basename(dirname(dirname(__FILE__))));
if (!defined("EVENTS_URL")) define("EVENTS_URL", ICMS_URL."/modules/".EVENTS_DIRNAME."/");
if (!defined("EVENTS_ROOT_PATH")) define("EVENTS_ROOT_PATH", ICMS_ROOT_PATH."/modules/".EVENTS_DIRNAME."/");
if (!defined("EVENTS_IMAGES_URL")) define("EVENTS_IMAGES_URL", EVENTS_URL."images/");
if (!defined("EVENTS_ADMIN_URL")) define("EVENTS_ADMIN_URL", EVENTS_URL."admin/");

// Include the common language file of the module
icms_loadLanguageFile("events", "common");
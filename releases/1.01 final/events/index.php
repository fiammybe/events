<?php
/**
 * User index page of the module
 *
 * Including the event page
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2013.
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		events
 * @version		$Id$
 */

include_once "../../mainfile.php";
include_once ICMS_ROOT_PATH . "/header.php";
 
$start_options = array(0 => 'event.php', 1 => 'calendar.php');
$start_page = $start_options[icms::$module->config['events_start_page']];
header('location: ' . $start_page);
exit();
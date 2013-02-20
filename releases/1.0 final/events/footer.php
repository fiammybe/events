<?php
/**
 * Footer page included at the end of each page on user side of the mdoule
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2013.
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		events
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

$icmsTpl->assign("events_adminpage", "<a href='" . ICMS_URL . "/modules/" . icms::$module->getVar("dirname") . "/admin/index.php'>" ._MD_EVENTS_ADMIN_PAGE . "</a>");
$icmsTpl->assign("events_is_admin", icms_userIsAdmin(EVENTS_DIRNAME));
$icmsTpl->assign('events_url', EVENTS_URL);
$icmsTpl->assign('events_images_url', EVENTS_IMAGES_URL);

$xoTheme->addStylesheet(EVENTS_URL . 'module' . ((defined("_ADM_USE_RTL") && _ADM_USE_RTL) ? '_rtl' : '') . '.css');

include_once ICMS_ROOT_PATH . '/footer.php';
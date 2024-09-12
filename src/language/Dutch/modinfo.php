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

define("_MI_EVENTS_MD_NAME", "Evenementen");
define("_MI_EVENTS_MD_DESC", "ImpressCMS Simple Events");
define("_MI_EVENTS_EVENTS", "Evenementen");
define("_MI_EVENTS_SHOW_BREADCRUMB", "Toon broodkruimel?");
define("_MI_EVENTS_SHOW_BREADCRUMB_DSC", "Schakelt de broodkruimel navigatiestrook aan en uit.");
define("_MI_EVENTS_TEMPLATES", "Sjablonen");
define("_MI_EVENTS_DATE_FORMAT", "Datum formaat");
define("_MI_EVENTS_DATE_FORMAT_DSC", "Geef het datumformaat op (zie de date() functie in de PHP handleiding voor modifier codes).");

// Blocks
define("_MI_EVENTS_UPCOMING", "Aankomende evenementen");
define("_MI_EVENTS_UPCOMINGDSC", "Toont toekomstige evenementen");
// Since 2.0
define("_MI_EVENTS_UPCOMING_MENU", "Upcoming events in menu");
define("_MI_EVENTS_UPCOMING_MENUDSC", "Displays forthcoming events in menu");


// Added in V1.01
define("_MI_EVENTS_CALENDAR", "Kalender");
define("_MI_EVENTS_LIST", "Overzicht");
define("_MI_EVENTS_START_PAGE", "Selecteer de startpagina van module");
define("_MI_EVENTS_START_PAGE_DSC", "Om evenementen lijst standaard weer te geven, selecteer 'event.php', om een agenda-stijl te selecteren selecteer kalender.php");
define("_MI_EVENTS_SHOW_TAG_SELECT_BOX", "Tag selectievak weergeven?");
define("_MI_EVENTS_SHOW_TAG_SELECT_BOX_DSC", "Hiermee kan de moduleinhoud worden gefilterd op tag. Werkt enkel wanneer de prockets module is geïnstalleerd");
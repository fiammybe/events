<?php
/**
 * English language constants commonly used in the module
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2013.
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		events
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

// Event
define("_CO_EVENTS_EVENT_TITLE", "Titel");
define("_CO_EVENTS_EVENT_TITLE_DSC", "Naam van het evenement.");
define("_CO_EVENTS_EVENT_DATE", "Begin datum");
define("_CO_EVENTS_EVENT_DATE_DSC", "Wanneer begint het evenement?");
define("_CO_EVENTS_EVENT_END_DATE", "Eind datum");
define("_CO_EVENTS_EVENT_END_DATE_DSC", "Wanneer eindigt het evenement?");
define("_CO_EVENTS_EVENT_DESCRIPTION", "Beschrijving");
define("_CO_EVENTS_EVENT_DESCRIPTION_DSC", "Voer de details van uw evenement in (optioneel). Indien u geen beschrijving opgeeft, wordt de module gekoppeld aan de evenement website (indien beschikbaar).");
define("_CO_EVENTS_EVENT_COVERAGE", "Locatie");
define("_CO_EVENTS_EVENT_COVERAGE_DSC", "Waar is het evenement? Voorgesteld formaat: Stad, Land of iets vergelijkbaars.");
define("_CO_EVENTS_EVENT_IDENTIFIER", "Website");
define("_CO_EVENTS_EVENT_IDENTIFIER_DSC", "Voer een link in naar de officiële website van het evenement, indien beschikbaar.");
define("_CO_EVENTS_EVENT_ONLINE_STATUS", "Online?");
define("_CO_EVENTS_EVENT_ONLINE_STATUS_DSC", "Schakelijk het evenement offline of online. Offline evenementen worden niet getoond in de zoekresultaten.");
define("_CO_EVENTS_EVENT_CREATOR", "Inzender");
define("_CO_EVENTS_EVENT_CREATOR_DSC", "Persoon die dit evenement heeft ingediend.");
define("_CO_EVENTS_EVENT_ONLINE", "Evenement is online");
define("_CO_EVENTS_EVENT_OFFLINE", "Evenement is offline");

// Calendar
define("_CO_EVENTS_CAL_JANUARY", "januari");
define("_CO_EVENTS_CAL_FEBRUARY", "februari");
define("_CO_EVENTS_CAL_MARCH", "maart");
define("_CO_EVENTS_CAL_APRIL", "april");
define("_CO_EVENTS_CAL_MAY", "mei");
define("_CO_EVENTS_CAL_JUNE", "juni");
define("_CO_EVENTS_CAL_JULY", "juli");
define("_CO_EVENTS_CAL_AUGUST", "augustus");
define("_CO_EVENTS_CAL_SEPTEMBER", "september");
define("_CO_EVENTS_CAL_OCTOBER,", "oktober");
define("_CO_EVENTS_CAL_NOVEMBER", "november");
define("_CO_EVENTS_CAL_DECEMBER", "december");

// Index page
define("_CO_EVENTS_NO_EVENTS", "Er zijn op dit moment geen toekomste evenementen.");

// Errors
define("_CO_EVENTS_ERROR_BAD_DATE", "Begindatum MOET voor de einddatum liggen.");
define("_CO_EVENTS_ERROR_BAD_URL", "Fout: URL is niet geldig. Controleer het formaat en probeer het opnieuw.");

// New in V1.02
define("_CO_EVENTS_EVENT_TAG", "tags");
define("_CO_EVENTS_EVENT_TAG_DSC", "Selecteer de tags (onderwerpen) waarmee u dit evenement wilt labelen.");
define("_CO_EVENTS_ALL", "Alles");
define("_CO_EVENTS_ALL_TAGS", "-- Alles --");
define("_CO_EVENTS_UNTAGGED", "Ongetagd");
define("_CO_EVENTS_EVENT_IMAGE", "Logo");
define("_CO_EVENTS_EVENT_IMAGE_DSC", "Het logo of afbeelding van de gebeurtenis. Alleen handig voor gebeurtenissen waarbij 
	× je een volledige beschrijving geeft, anders wordt het niet getoond.");
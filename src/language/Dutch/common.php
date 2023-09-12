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
define("_CO_EVENTS_EVENT_TITLE", "Title");
define("_CO_EVENTS_EVENT_TITLE_DSC", "Name of the event.");
define("_CO_EVENTS_EVENT_DATE", "Start date");
define("_CO_EVENTS_EVENT_DATE_DSC", "When does the event start?");
define("_CO_EVENTS_EVENT_END_DATE", "End date");
define("_CO_EVENTS_EVENT_END_DATE_DSC", "When does the event finish?");
define("_CO_EVENTS_EVENT_DESCRIPTION", "Description");
define("_CO_EVENTS_EVENT_DESCRIPTION_DSC", "Enter the details of your event (optional). If you do not provide a description, the module will link out to the event website instead (if available).");
define("_CO_EVENTS_EVENT_COVERAGE", "Location");
define("_CO_EVENTS_EVENT_COVERAGE_DSC", "Where is the event? Suggested format: City, Country or similar.");
define("_CO_EVENTS_EVENT_IDENTIFIER", "Website");
define("_CO_EVENTS_EVENT_IDENTIFIER_DSC", "Enter a link to the official event website, if available.");
define("_CO_EVENTS_EVENT_ONLINE_STATUS", "Online?");
define("_CO_EVENTS_EVENT_ONLINE_STATUS_DSC", "Toggle the event on or offline. Events that are offline will not be visible and will not be returned in search results.");
define("_CO_EVENTS_EVENT_CREATOR", "Submitter");
define("_CO_EVENTS_EVENT_CREATOR_DSC", "Person that submitted this event.");
define("_CO_EVENTS_EVENT_ONLINE", "Event is online");
define("_CO_EVENTS_EVENT_OFFLINE", "Event is offline");

// Calendar
define("_CO_EVENTS_CAL_JANUARY", "January");
define("_CO_EVENTS_CAL_FEBRUARY", "February");
define("_CO_EVENTS_CAL_MARCH", "March");
define("_CO_EVENTS_CAL_APRIL", "April");
define("_CO_EVENTS_CAL_MAY", "May");
define("_CO_EVENTS_CAL_JUNE", "June");
define("_CO_EVENTS_CAL_JULY", "July");
define("_CO_EVENTS_CAL_AUGUST", "August");
define("_CO_EVENTS_CAL_SEPTEMBER", "September");
define("_CO_EVENTS_CAL_OCTOBER,", "October");
define("_CO_EVENTS_CAL_NOVEMBER", "November");
define("_CO_EVENTS_CAL_DECEMBER", "December");

// Index page
define("_CO_EVENTS_NO_EVENTS", "There are no upcoming events listed at this time.");

// Errors
define("_CO_EVENTS_ERROR_BAD_DATE", "Start date MUST precede end date.");
define("_CO_EVENTS_ERROR_BAD_URL", "Error: URL is not valid. Please check format and try again.");

// New in V1.02
define("_CO_EVENTS_EVENT_TAG", "Tags");
define("_CO_EVENTS_EVENT_TAG_DSC", "Select the tags (subjects) you wish to label this event with.");
define("_CO_EVENTS_ALL", "All");
define("_CO_EVENTS_ALL_TAGS", "-- All --");
define("_CO_EVENTS_UNTAGGED", "Untagged");
define("_CO_EVENTS_EVENT_IMAGE", "Logo");
define("_CO_EVENTS_EVENT_IMAGE_DSC", "The logo or image of the event. Only useful for events where 
	you are providing a full description, as otherwise it will not be displayed.");
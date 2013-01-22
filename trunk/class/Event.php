<?php
/**
 * Class representing Events event objects
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2013.
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		events
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

class mod_events_Event extends icms_ipf_seo_Object {
	/**
	 * Constructor
	 *
	 * @param mod_events_Event $handler Object handler
	 */
	public function __construct(&$handler) {
		icms_ipf_object::__construct($handler);

		$this->quickInitVar("event_id", XOBJ_DTYPE_INT, TRUE);
		$this->quickInitVar("title", XOBJ_DTYPE_TXTBOX, TRUE);
		$this->quickInitVar("date", XOBJ_DTYPE_LTIME, TRUE); // Starting date of event
		$this->quickInitVar("end_date", XOBJ_DTYPE_LTIME, TRUE);
		$this->quickInitVar("description", XOBJ_DTYPE_TXTAREA, FALSE);
		$this->quickInitVar("coverage", XOBJ_DTYPE_TXTBOX, TRUE); // Location
		$this->quickInitVar("identifier", XOBJ_DTYPE_TXTBOX, FALSE); // Official event website
		$this->quickInitVar("creator", XOBJ_DTYPE_INT, TRUE); // Submitter
		$this->quickInitVar("online_status", XOBJ_DTYPE_INT, TRUE, FALSE, FALSE, 1); // Toggles on or offline
		$this->initCommonVar("counter");
		$this->initCommonVar("dohtml");
		$this->initCommonVar("dobr");
		$this->setControl('creator', 'user');
		$this->setControl("online_status", "yesno");
		
		$this->initiateSEO();
	}

	/**
	 * Overriding the icms_ipf_Object::getVar method to assign a custom method on some
	 * specific fields to handle the value before returning it
	 *
	 * @param str $key key of the field
	 * @param str $format format that is requested
	 * @return mixed value of the field that is requested
	 */
	public function getVar($key, $format = "s") {
		if ($format == "s" && in_array($key, array('creator', 'online_status'))) {
			return call_user_func(array ($this,	$key));
		}
		return parent::getVar($key, $format);
	}
	
	/*
     * Converts user id to human readable user name
	*/
	public function creator() {
		return icms_member_user_Handler::getUserLink($this->getVar('creator', 'e'));
	}
	
	/*
     * Converts status field to clickable icon that can change status
	*/
	public function online_status() {
		$button = '';
		$status = $this->getVar('online_status', 'e');

		$button = '<a href="' . ICMS_URL . '/modules/' . basename(dirname(dirname(__FILE__)))
				. '/admin/event.php?event_id=' . $this->getVar('event_id')
				. '&amp;op=changeStatus">';
		if ($status == '1') {
			$button .= '<img src="' . ICMS_IMAGES_SET_URL . '/actions/button_ok.png" alt="' 
				. _CO_EVENTS_EVENT_ONLINE . '" title="'
				. _CO_EVENTS_EVENT_ONLINE . '" /></a>';
		} else {
			$button .= '<img src="' . ICMS_IMAGES_SET_URL . '/actions/button_cancel.png" alt="'
				. _CO_EVENTS_EVENT_OFFLINE . '" title="'
				. _CO_EVENTS_EVENT_OFFLINE . '" /></a>';
		}
		return $button;
	}
}
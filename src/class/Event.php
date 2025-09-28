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
		$this->initNonPersistableVar('tag', XOBJ_DTYPE_INT, 'tag', FALSE, FALSE, FALSE, TRUE);
		$this->quickInitVar("description", XOBJ_DTYPE_TXTAREA, FALSE);
		$this->quickInitVar("coverage", XOBJ_DTYPE_TXTBOX, TRUE); // Location
		$this->quickInitVar("identifier", XOBJ_DTYPE_TXTBOX, FALSE); // Official event website
		$this->quickInitVar("image", XOBJ_DTYPE_IMAGE, FALSE);
		$this->quickInitVar("creator", XOBJ_DTYPE_INT, TRUE); // Submitter
		$this->quickInitVar("online_status", XOBJ_DTYPE_INT, TRUE, FALSE, FALSE, 1); // Toggles on or offline
		$this->quickInitVar('type', XOBJ_DTYPE_TXTBOX, TRUE, FALSE, FALSE, 'Event'); // Internal admin purposes only
		$this->initCommonVar("counter");
		$this->initCommonVar("dohtml");
		$this->initCommonVar("dobr");
		
		// Set controls
		$this->setControl('image', 'imageupload');
		$this->setControl('creator', 'user');
		$this->setControl("online_status", "yesno");
		
		// Only display the tag field if the sprockets module is installed
		if (icms_get_module_status("sprockets"))
		{
			$this->setControl('tag', array(
			'name' => 'selectmulti',
			'itemHandler' => 'tag',
			'method' => 'getTags',
			'module' => 'sprockets'));
			
			$this->setControl('category', array(
			'name' => 'selectmulti',
			'itemHandler' => 'tag',
			'method' => 'getCategoryOptions',
			'module' => 'sprockets'));
		}
		else 
		{
			$this->hideFieldFromForm('tag');
			$this->hideFieldFromSingleView ('tag');
		}
		
		// Hide the 'type' field, which is for internal admin purposes only
		$this->hideFieldFromForm('type');
		$this->hideFieldFromSingleView('type');
		
		// The image field has been added for Sprockets compatibility reasons, but is not currently
		// in use, therefore it will remain hidden until the functionality is implemented
		$this->hideFieldFromForm('image');
		$this->hideFieldFromSingleView('image');
		
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
	
	/**
     * Load tags linked to this Event
     *
     * @return void
     */
     public function loadTags() {
          
		$ret = array();
		$sprocketsModule = icms_getModuleInfo('sprockets');
          
		// Retrieve the tags for this object
		if (icms_get_module_status("sprockets")) {
		   $sprockets_taglink_handler = icms_getModuleHandler('taglink',
				   $sprocketsModule->getVar('dirname'), 'sprockets');

		   // label_type = 0 means only return tags
		   $ret = $sprockets_taglink_handler->getTagsForObject($this->id(), $this->handler, '0');
		   $this->setVar('tag', $ret);
		}
     }
}
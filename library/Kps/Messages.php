<?php
/**
 * Class to save and show messages. Saves messages in session. 
 *
 * @author Konstantin Shamko <konstantin.shamko@gmail.com> 
 * @version 0.0.1
 * @copyright  Copyright (c) 2009 Konstantin Shamko
 * @category VaselinEngine
 * @package Bel Classes
 * @license  New BSD License
 */ 
class Kps_Messages {
	/**
	 *  Instance of massages class to use singleton pattern
	 *
	 * @var Kps_Messages object
	 */
	protected static $_instance = null;
	/**
	 * Session to store messages
	 *
	 * @var Zend_Session_mNamespace object
	 */	
	protected $session;
	/**
	 * Constructor. Creates session
	 *
	 */	
	public function __construct() {
		$this->session = new Zend_Session_Namespace ( 'messages' );
	}
	/**
	 * Singleton realization. Call this to get instance of this class
	 *
	 * @return Kps_Messages
	 */
	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self ( );
		}
		
		return self::$_instance;
	}
	/**
	 * Adds message to session
	 *
	 * @param string $message - message text
	 * @param string $type - 'error' or anything else
	 * @param string $key - if error was generated after form validation. $key - name of the incorrect field 
	 */
	public function setMessage($message, $type = false, $key = NULL) {
		if ($key) {
			$this->session->messages[$key] = array('message'=>$message, 'type'=>$type);
		} else {
			$this->session->messages[] = array('message'=>$message, 'type'=>$type);
		}
	}
	/**
	 * Return messages array and clear message's session
	 *
	 * @return array - rray with messages
	 */
	public function getMessages() {
		$messages = $this->session->messages;
		$this->session->unsetAll ();
		return $messages;
	}
}
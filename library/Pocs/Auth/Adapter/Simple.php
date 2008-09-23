<?php

require_once 'Zend/Auth/Adapter/Interface.php';
require_once 'library/Pocs/Identity.php';

class Pocs_Auth_Adapter_Simple implements Zend_Auth_Adapter_Interface {
	
	public $un;
	public $pw;
	
	/**
	 * Identity of authenticated user
	 *
	 * @var Pocs_Identity
	 */
	public $identity;
	
	/**
	 * Array of simple credentials in array (username => password)
	 *
	 * @var array
	 */
	private $credentials = array ( );
	
	/**
	 * Array of identities
	 * 
	 * @var array
	 */
	private $identities = array ( );
	
	public function __construct($un, $pw, array $credentials, array $identities) {
		$this->un = $un;
		$this->pw = $pw;
		$this->credentials = $credentials;
		$this->identities = $identities;
		$this->identity = new Pocs_Identity ( );
	}
	/**
	 * 
	 * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed 
	 * @return Zend_Auth_Result 
	 * @see Zend_Auth_Adapter_Interface::authenticate()
	 */
	public function authenticate() {
		if (! array_key_exists ( $this->un, $this->credentials ) || ! array_key_exists ( $this->un, $this->identities )) {
			return new Zend_Auth_Result ( Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, NULL );
		}
		$this->identity = $this->identities[$this->un];
		return ($this->pw === $this->credentials[$this->un]) ? new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,$this->identity) : new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID);
	}
}

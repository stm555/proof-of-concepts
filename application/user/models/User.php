<?php

/**
 * system user
 *  
 * @author stm
 * @version 
 */
require_once 'Zend/Auth.php';
require_once 'Zend/Acl.php';
require_once 'Zend/Acl/Role.php';
require_once 'Zend/Acl/Resource.php';
require_once 'library/Pocs/Auth/Adapter/Simple.php';

class Pocs_User {

	public $loginName;
	public $identity;
	
	/**
	 * Authentication Mechanism
	 *
	 * @var Zend_Auth_Adapter_Interface
	 */
	private $authenticator;
	
	/**
	 * Security definitions
	 * @var Zend_Acl
	 */
	private $acl;
	
	public function __construct(Zend_Auth_Adapter_Interface $auth, Zend_Acl $acl)
	{
		$this->authenticator = $auth;
		$this->acl = $acl;
	}
	
	/**
	 * Attempt to get an authenticated user object with the provided username and password
	 *
	 * @param string $user
	 * @param string $password
	 * @return Pocs_User
	 * @throws exception on failure to login
	 */
	public static function login($un, $pw, Zend_Auth_Adapter_Interface $auth, Zend_Acl $acl)
	{
		//BUG this only works with the test adapter I created .. problem with design when auth adapter all ready contains the credentials
		//want to support authentication with different kind of credentials but this assumes that credentials are always a un/pw pair
		//otherwise the credential setting all has to happen outside of login .. 
		
		$auth->un = $un;
		$auth->pw = $pw; 
		
		$user = new Pocs_User($auth, $acl);
		
		$result = $user->authenticator->authenticate();
		if (!$result->isValid())
		{
			throw new Exception('Failed to Login',$result->getCode());
		}
		
		$user->loginName = $un;
		$user->identity = $result->getIdentity();
		
		return $user;
	}
	
	/**
	 * Retrieve ACL used for this user
	 *
	 * @return Zend_Acl
	 */
	public function getAcl()
	{
		return $this->acl;
	}
	
	/**
	 * Check if user has privilige on resource
	 *
	 * @param string $privilege
	 * @param Zend_Acl_Resource_Interface|string $resource
	 * @return boolean
	 */
	public function may($privilege, $resource)
	{
		return $this->acl->isAllowed($this->loginName, $resource, $privilege);
	}
}

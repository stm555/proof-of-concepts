<?php

require_once 'application/user/models/User.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Pocs_User test case.
 */
class Pocs_UserTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	protected function _generateAcl()
	{
		//This would probably be pulled from a registry or something in an application
		//hard coded here to an example ACL
		//create the acl
		$acl = new Zend_Acl();
		
		//create playlist resource
		$acl->add(new Zend_Acl_Resource('playlist'));
		
		//Listen can view playlists
		$acl->addRole(new Zend_Acl_Role('listener'));
		$acl->allow('listener','playlist','view');
		
		//DJ inherits from listener to view playlists, but can also play playlists
		$acl->addRole(new Zend_Acl_Role('dj'),'listener');
		$acl->allow('dj','playlist','play');

		//program manager inherits from DJ to view and play playlists but can also manage playlists
		$acl->addRole(new Zend_Acl_Role('program manager'),'dj');
		$acl->allow('program manager', 'playlist', 'manage');
		
		return $acl;
	}
	
	protected function _generateAuth($un, $pw)
	{
		//this would probably be pulled from a registry or something in an application
		//hard coded here to the simple auth adapter with hard coded values
		$identity = new Pocs_Identity();
		$identity->firstName = 'Seth';
		$identity->lastName = 'Thornberry';
		$identity->salutation = 'The Man';
		return new Pocs_Auth_Adapter_Simple($un, $pw, array ($un => $pw), array($un => $identity));
	}
	
	public function testLoginReturnsUserObject() {
		$loggedInUser = Pocs_User::login ( 'stm', 'smartone', $this->_generateAuth('stm', 'smartone'), $this->_generateAcl() );
		$this->assertType ( 'Pocs_User', $loggedInUser );
	}
	
	public function testLoginInvalidUser() {
		try {
			Pocs_User::login ( 'invalidUser', 'invalidPass', $this->_generateAuth('stm', 'smartone'), $this->_generateAcl() );
		} catch ( Exception $e ) {
			return; //Pass
		}
		$this->fail ( 'Should not be able to login an invalid user' );
	}

	public function testLoginNameSetCorrectly()
	{
		$un = 'stm';
		$user = Pocs_User::login ( $un, 'smartone', $this->_generateAuth($un, 'smartone'), $this->_generateAcl() );
		$this->assertEquals($un,$user->loginName);
	}
	
	public function testIdentitySetCorrectly() {
		$user = Pocs_User::login ( 'stm', 'smartone', $this->_generateAuth('stm', 'smartone'), $this->_generateAcl() );
		//This test requires some knowledge of the hard codes in the class..
		$this->assertEquals ( $user->identity->firstName, 'Seth' );
		$this->assertEquals ( $user->identity->lastName, 'Thornberry' );
	}
	
	public function testGetAcl() {
		$user = Pocs_User::login ( 'stm', 'smartone', $this->_generateAuth('stm', 'smartone'), $this->_generateAcl() );
		$this->assertType('Zend_Acl', $user->getAcl());
	}
	
	public function testMay()
	{
		$user = Pocs_User::login ( 'stm', 'smartone', $this->_generateAuth('stm', 'smartone'), $this->_generateAcl() );
		
		//Add a resource and permission for stm to the acl for this user
		$acl = $user->getAcl();
		$acl->addRole(new Zend_Acl_Role($user->loginName));
		$acl->add(new Zend_Acl_Resource('foo'));
		$acl->allow($user->loginName,'foo','read');
		
		$this->assertTrue($user->may('read','foo'));
	}
	
	public function testMayNot()
	{
		$user = Pocs_User::login ( 'stm', 'smartone', $this->_generateAuth('stm', 'smartone'), $this->_generateAcl() );
		
		//Add a resource and permission for stm to the acl for this user
		$acl = $user->getAcl();
		$acl->addRole(new Zend_Acl_Role($user->loginName));
		$acl->add(new Zend_Acl_Resource('foo'));
		//Not adding an allow for this new resource
		
		$this->assertFalse($user->may('read','foo'));
	}
	
	public function testMayInherited()
	{
		$user = Pocs_User::login ( 'stm', 'smartone', $this->_generateAuth('stm', 'smartone'), $this->_generateAcl() );
		
		//Add a resource and permission for stm to the acl for this user
		$acl = $user->getAcl();
		$acl->addRole(new Zend_Acl_Role('group'));
		$acl->addRole(new Zend_Acl_Role($user->loginName),'group');
		$acl->add(new Zend_Acl_Resource('foo'));
		$acl->allow('group','foo','read');
		
		$this->assertTrue($user->may('read','foo'));
	}
}


<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/local/Zend/ZendFramework/Library');

require_once 'library/Pocs/Auth/Adapter/Simple.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Pocs_Auth_Adapter_Simple test case.
 */
class Pocs_Auth_Adapter_SimpleTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Pocs_Auth_Adapter_Simple
	 */
	private $Pocs_Auth_Adapter_Simple;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$un = 'stm';
		$pw = 'smartone';
		$identity = new Pocs_Identity();
		$identity->firstName = 'Seth';
		$identity->lastName = 'Thornberry';
		$identity->salutation = 'The Man';

		$this->Pocs_Auth_Adapter_Simple = new Pocs_Auth_Adapter_Simple($un,$pw,array($un => $pw),array($un => $identity));
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated Pocs_Auth_Adapter_SimpleTest::tearDown()
		

		$this->Pocs_Auth_Adapter_Simple = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
	}
	
	/**
	 * Tests Pocs_Auth_Adapter_Simple->authenticate()
	 */
	public function testAuthenticateType() {
						
		$result = $this->Pocs_Auth_Adapter_Simple->authenticate();
		$this->assertType('Zend_Auth_Result', $result);
	
	}
	
	public function testAuthenticateValid() {
		$result = $this->Pocs_Auth_Adapter_Simple->authenticate();
		$this->assertTrue($result->isValid());	
	}

}


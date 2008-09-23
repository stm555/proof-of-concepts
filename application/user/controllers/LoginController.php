<?php

/**
 * loginController
 * 
 * @author stm
 * @version 
 */

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Form.php';
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/Form/Element/Password.php';
require_once 'application/user/models/User.php';
require_once 'Zend/Debug.php';

class User_LoginController extends Zend_Controller_Action {
	/**
	 * flash messenger object
	 *
	 * @var Zend_Controller_Action_Helper_FlashMessenger
	 */
	protected $_flashMessenger = null;
	
	public function init()
	{
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}
	
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->view->messages = $this->_flashMessenger->getMessages();
		
		$loginForm = new Zend_Form();
		$unField = new Zend_Form_Element_Text('un');
		$unField->setLabel('User Name');
		$pwField = new Zend_Form_Element_Password('pw');
		$pwField->setLabel('Password');
		$loginForm->addElement($unField);
		$loginForm->addElement($pwField);
		$loginForm->addDisplayGroup(array('un','pw'),'authGroup')->getDisplayGroup('authGroup')->setLegend('Authentication');
		
		//view specific form additions
		//TODO: not sure if $_SERVER['HTTP_HOST'] is dependable enough to use in this case .. XSS vulnerability
		//TODO: programmatically determine http/https
		$loginForm->setAction('http://' . $_SERVER['HTTP_HOST'] . $this->getFrontController()->getBaseUrl() . $this->_helper->url('authenticate'));
		$loginForm->addElement('submit','login');
		
		$this->view->loginForm = $loginForm;
	}
	
	public function authenticateAction() {
			
		try { $user = Pocs_User::login($this->getRequest()->getParam('un'), $this->getRequest()->getParam('pw'), $this->_getAuth(), $this->_getAcl()); }
		catch (Exception $e)
		{
			 $this->_flashMessenger->addMessage($e->getMessage());
			 $this->_helper->redirector('index','login');
		}
		$this->_flashMessenger->addMessage(Zend_Debug::dump($user,'Authenticated User', false));
		//TODO: Redirect somewhere useful..
		$this->_helper->redirector('index','login');
	}
	
	protected function _getAuth()
	{
		//this should come out of the application for what the auth adapter is and what their acl is
		$identity = new Pocs_Identity();
		$identity->firstName = 'Seth';
		$identity->lastName = 'Thornberry';
		$identity->salutation = 'The Man';
		return new Pocs_Auth_Adapter_Simple(null, null, array ('stm' => 'smartone'), array('stm' => $identity));
	}
	
	protected function _getAcl()
	{
		return new Zend_Acl();
	}

}

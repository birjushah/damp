<?php
namespace User\Controller;

use Standard\Controller\AbstractController;
use Zend\View\Model\ViewModel;

class LoginController extends AbstractController
{

    public function indexAction ()
    {
        $loginFormFilter = new \User\FormFilter\LoginFormFilter();
        
        $loginForm = new \User\Form\LoginForm();
        $loginForm->setInputFilter($loginFormFilter->getInputFilter());
        $request = $this->getRequest();
		$message = '';
        if ($request->isPost()) {
            $loginForm->setData($request->getPost());
            if ($loginForm->isValid()) {
                $data = $loginForm->getData();
                
                // Configure Doctrine Adapter for authentication
                $doctrineAdapter = new \DoctrineModule\Authentication\Adapter\ObjectRepository();
                $config = $this->getServiceLocator()->get('Config');
                $config = $config['doctrine']['authenticationadapter']['odm_default'];
                
                if (is_string($config['objectManager'])) {
                    $config['objectManager'] = $this->getServiceLocator()->get($config['objectManager']);
                }
                // Initialize the Doctrine Adapter with options
                $doctrineAdapter->setOptions($config);
                
                // Set the received credentials
                $doctrineAdapter->setIdentityValue((string) $data['username']);
                $doctrineAdapter->setCredentialValue((string) $data['password']);
                
                
                // Get the user auth mechanism
                $userAuth = $this->getServiceLocator()->get('Standard\Permissions\Authentication\Authentication');
                
                // Tell the user auth about the doctrine adapter
                $userAuth->setAuthAdapter($doctrineAdapter);
                $authService = $userAuth->getAuthService();
                $authenticationResult = $authService->authenticate($userAuth->getAuthAdapter());
                if ($authenticationResult->isValid()) {
                    $user = $authenticationResult->getIdentity();
                    return $this->redirect()->toRoute('admin', array(
							'controller' => 'index',
							'action' => 'index'
					));
                } else {
					$message = "Invalid Credentials";
                }
            } else {
                $message = $loginForm->getMessages();
                die();
            }
        }
        $this->layout("login/layout");
        $view = new ViewModel(array(
            'loginForm' => $loginForm,
			'message' => $message
        ));
        
        return $view;
    }
}

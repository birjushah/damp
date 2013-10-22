<?php
namespace User\Controller;

use Standard\Controller\AbstractController;

class LogoutController extends AbstractController
{

    public function indexAction ()
    {
        $userAuth = $this->getServiceLocator ()->get ( 'Standard\Permissions\Authentication\Authentication' );
		$userAuthService = $userAuth->getAuthService ();
		if ($userAuthService->hasIdentity ()) {
			$identity = $userAuthService->getIdentity ();
			if ($identity->getGroupName() != \Standard\Permissions\Acl\Acl::DEFAULT_ROLE) {
				$userAuthService->clearIdentity ();
			}
		}
		return $this->redirect ()->toRoute ( 'home' );
    }
}

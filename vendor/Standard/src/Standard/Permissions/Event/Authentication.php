<?php

/**
 * File for Event Class
 *
 * @category  User
 * @package   User_Event
 * @author    Marco Neumann <webcoder_at_binware_dot_org>
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/index/type:new-bsd New BSD License
 */

/**
 *
 * @namespace Standard\Permissons\Event
 */
namespace Standard\Permissions\Event;

/**
 *
 * @uses Zend\Mvc\MvcEvent
 * @uses User\Controller\Plugin\UserAuthentication
 * @uses User\Acl\Acl
 */
use Zend\Mvc\MvcEvent as MvcEvent;
use Standard\Permissions\Authentication\Authentication as AuthPlugin;
use Standard\Permissions\Acl\Acl as AclClass;
use Standard\StaticOptions\StaticOptions as StaticOptions;

/**
 * Authentication Event Handler Class
 *
 * This Event Handles Authentication
 *
 * @category User
 * @package User_Event
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license http://binware.org/license/index/type:new-bsd New BSD License
 */
class Authentication
{

    /**
     *
     * @var AuthPlugin
     */
    protected $_userAuth = null;

    /**
     *
     * @var AclClass
     */
    protected $_aclClass = null;

    /**
     *
     * @var \Zend\Mvc\MvcEvent $event
     */
    protected $_event = null;

    /**
     * Store all the groups
     *
     * @var $_groups Array
     */
    protected $_groups = array();

    /**
     * Store the current role of user
     *
     * @var $_currentRole Default: GUEST
     */
    protected $_currentRole = "GUEST";

    /**
     * Store the current user id
     *
     * @var $_currentUserId Default: 0
     */
    protected $_currentUserId = 0;
	
	/**
     * Store the redirect403 configuration
     *
     * @var $_redirect403 Default: 0
     */
	protected $_redirect403 = array();
	
    /**
     * preDispatch Event Handler
     *
     * @param \Zend\Mvc\MvcEvent $event            
     * @throws \Exception
     */
    public function preDispatch (MvcEvent $event)
    {
        $userAuth = $this->getUserAuthenticationPlugin();
        $this->_event = $event;
        
        // Initialize AclClass groups
        $groups = $this->getEntityManager()
            ->getRepository('Standard\Entity\Group')
            ->findAll();
        
        foreach ($groups as $group) {
            $this->_groups[$group->getGroupId()] = $group->getGroupName();
        }
        // Add the groups to ACL class
        AclClass::$groups = $this->_groups;
        
        // Get default role
        $role = AclClass::DEFAULT_ROLE;

        if ($userAuth->hasIdentity()) {
            $user = $userAuth->getIdentity();
            $user->setGroupName(\Standard\Permissions\Acl\Acl::$groups[$user->getGroupId()]);
        } else {
            $user = StaticOptions::getGuestUserObject();
        }
        $role = $user->getGroupName();
        $this->_currentRole = $user->getGroupName();
        $this->_currentUserId = $user->getUserId();
        
        // Get the ACL class
        $acl = $this->getAclClass();
        
        $routeMatch = $event->getRouteMatch();
		//var_dump($routeMatch->getParam('__NAMESPACE__'));die;
		$namespace = $routeMatch->getParam('__NAMESPACE__');
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');
		
		$redirectUrl = false;
		
		if($namespace == "Admin\Controller") {
			if (! $acl->hasResource($controller) || ! $acl->isAllowed($role, $controller, $action)) {
				$searchKey = StaticOptions::recursive_array_search($namespace, $this->_redirect403);
				if( $searchKey!== false){
					$redirectArray = $this->_redirect403[$searchKey];
					if(isset($redirectArray['redirect-group']) && isset($redirectArray['redirect-group'][$this->_currentRole])){
						$redirectUrl = $redirectArray['redirect-group'][$this->_currentRole];
					}
				} 
				if($redirectUrl === false){
					return StaticOptions::get403Response();
				} else {
					return StaticOptions::get302Response($redirectUrl);
				}
			}
		}
    }

    /**
     * Sets Authentication Plugin
     *
     * @param \User\Controller\Plugin\UserAuthentication $userAuthenticationPlugin            
     * @return Authentication
     */
    public function setUserAuthenticationPlugin (AuthPlugin $userAuthenticationPlugin)
    {
        $this->_userAuth = $userAuthenticationPlugin;
        
        return $this;
    }

    /**
     * Gets Authentication Plugin
     *
     * @return \User\Controller\Plugin\UserAuthentication
     */
    public function getUserAuthenticationPlugin ()
    {
        if ($this->_userAuth === null) {
            $this->_userAuth = new AuthPlugin();
        }
        
        return $this->_userAuth;
    }

    /**
     * Sets ACL Class
     *
     * @param \User\Acl\Acl $aclClass            
     * @return Authentication
     */
    public function setAclClass (AclClass $aclClass)
    {
        $this->_aclClass = $aclClass;
        
        return $this;
    }

    /**
     * Gets ACL Class
     *
     * @return \User\Acl\Acl
     */
    public function getAclClass ()
    {
        if ($this->_aclClass === null) {
            $aclArray = $this->getAclConfig();
            $this->_aclClass = new AclClass($aclArray);
        }
        
        return $this->_aclClass;
    }

    public function getAclConfig ()
    {
        $serviceLocator = $this->getServiceLocator();
        
        // Initializing Roles
        $roles = array();
        
        $roles[$this->_currentRole] = null;
        
        $roles[$this->_currentUserId] = null;
        
        // Get current account type id
        $current_group_id = array_search($this->_currentRole, $this->_groups);
        
        $current_user_id = $this->_currentUserId;
        
        $resources = array();
        
        // Get the guards(from acl configurations)
        $config = $serviceLocator->get('Config');
        $guard = $config['acl']['guard'];
		$this->_redirect403 = isset($config['acl']) && isset($config['acl']['403-Redirect']) ? $config['acl']['403-Redirect'] : array();
        foreach ($guard as $resourceName => $actionArray) {
            if (is_array($actionArray) && ! empty($actionArray)) {
                $action = array_keys($actionArray);
                $action = array_pop($action);
                if ($action != null) {
                    if (in_array($this->_currentRole, $actionArray[$action]['groups'])) {
                        $access = strtolower("ALLOW");
                        if (! isset($resources[$access])) {
                            $resources[$access] = array();
                        }
                        if (! isset($resources[$access][$resourceName])) {
                            $resources[$access][$resourceName] = array();
                        }
                        if (! isset($resources[$access][$resourceName][$action])) {
                            $resources[$access][$resourceName][$action] = "";
                        }
                        $resources[$access][$resourceName][$action] = array_keys($roles);
                    }
                }
            }
        }
        
        $aclConfig = array(
            'acl' => array(
                'roles' => $roles,
                'resources' => $resources
            )
        );
        AclClass::$config = $aclConfig;
        AclClass::$groups = $this->_groups;
        return $aclConfig;
    }

    public function getCurrentRole ()
    {
        return $this->_currentRole;
    }

    private function getEntityManager ()
    {
        if ($this->_event == null)
            throw new \Exception("Event variable not initialized");
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }

    private function getServiceLocator ()
    {
        if ($this->_event == null)
            throw new \Exception("Event variable not initialized");
        return $this->_event->getTarget()->getServiceLocator();
    }
}
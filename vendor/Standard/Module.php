<?php
/**
 * Standard Module
 *
 */

namespace Standard;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\EventManager\StaticEventManager;
use Standard\StaticOptions\StaticOptions as StaticOptions;

class Module
{
	protected $_namespace = __NAMESPACE__;
	protected $_dir = __DIR__;
	
	public function init() {
		if($this->_namespace == "Standard"){ 
			$events = StaticEventManager::getInstance ();
			
			$events->attach ( 'Zend\Mvc\Controller\AbstractActionController', 'dispatch', array (
					$this,
					'addServiceLocatorGlobally' 
			), 111 );
			
			// Add event of authentication before dispatch
			$events->attach ( 'Zend\Mvc\Controller\AbstractActionController', 'dispatch', array (
					$this,
					'authPreDispatch' 
			), 110 );
			
			// Add event of authentication before dispatch
			$events->attach ( 'Zend\Mvc\Controller\AbstractActionController', 'dispatch', array (
					$this,
					'authUserAuth' 
			), 109 );
		}
	}
	
	/**
	 * MVC preDispatch Event
	 *
	 * @param MvcEvent $event        	
	 * @return mixed
	 */
	public function authUserAuth($event) {
		$di = $event->getTarget ()->getServiceLocator ();
		$userAuth = $di->get ( 'Standard\Permissions\Authentication\Authentication' );
		if ($userAuth->hasIdentity ()) {
			$identity = $userAuth->getIdentity ();
		} else {
			$storage = new \Zend\Authentication\Storage\Session ();
			$identity = StaticOptions::getGuestUserObject ();
			$storage->write ( $identity );
		}
		StaticOptions::setCurrentUser ( $identity );
	}
	
	/**
	 * MVC preDispatch Event
	 *
	 * @param MvcEvent $event        	
	 * @return mixed
	 */
	public function addServiceLocatorGlobally($event) {
		$di = $event->getTarget ()->getServiceLocator ();
		StaticOptions::setServiceLocator ( $di );
	}
	
	/**
	 * MVC preDispatch Event
	 *
	 * @param MvcEvent $event        	
	 * @return mixed
	 */
	public function authPreDispatch($event) {
		$di = $event->getTarget ()->getServiceLocator ();
		$auth = $di->get ( 'Standard\Permissions\Event\Authentication' );
		return $auth->preDispatch ( $event );
	}
	
    public function getConfig()
    {
        // Generate invokables array
		$invokables = array ();
		
		// Initialize Route Array
		$routes = array ();
		
		// Initialize Template Array
		$templatePathStack = array ();
		$doctrineConfiguration = array(
            'driver' => array(
                $this->_namespace . '_driver' => array(
                    'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                    'cache' => 'array',
                    'paths' => array(
                        $this->_dir . '/src/' . $this->_namespace . '/Entity'
                    )
                ),
                'orm_default' => array(
                    'drivers' => array(
                        $this->_namespace . '\Entity' => $this->_namespace . '_driver'
                    )
                )
            )
        );
		
		// Get all controllers of the current module
		$controllerPath = $this->_dir . '/src/' . $this->_namespace . '/Controller/';
		$allController = array ();
		if (is_dir ( $controllerPath )) {
			$allController = scandir ( $controllerPath );
		}
		
		// Translator configurations
    	$translator = array();
        
        // Translator configurations
        $local_language_base_dir = $this->_dir . "/../language";
        
        // check if local language dir is available or not
        // If not then use the Application language dir
        if (is_dir($local_language_base_dir)) {
            $language_base_dir = $local_language_base_dir;
            $translator = array(
                'service_manager' => array(
                    'factories' => array(
                        'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory'
                    )
                ),
                'locale' => 'en_US',
                'translation_file_patterns' => array(
                    array(
                        'type' => 'gettext',
                        'base_dir' => $language_base_dir,
                        'pattern' => '%s.mo'
                    )
                )
            );
        }
		
		// Construct all necessary controller invokables list
		foreach ( $allController as $controller ) {
			if (is_file ( $controllerPath . DS . $controller )) {
				
				$controllerName = str_replace ( "Controller.php", "", $controller );
				$postfixControllerName = $controllerName . 'Controller';
				$key = $this->_namespace . "\\Controller\\" . $controllerName;
				$value = $this->_namespace . "\\Controller\\" . $postfixControllerName;
				$invokables [$key] = $value;
			}
		}
		
		// Construct Route
		$dashedNamespace = $this->convertToDash ( $this->_namespace );
		$routes [$dashedNamespace] = array (
				'type' => 'segment',
				'options' => array (
						'route' => "/" . $dashedNamespace . '[/:controller[/:action]]',
						'defaults' => array (
								'__NAMESPACE__' => $this->_namespace . "\\Controller",
								'controller' => $this->_namespace . "\\Controller\\Index",
								'action' => 'index' 
						) 
				),
				'may_terminate' => true,
				'child_routes' => array (
						'wildcard' => array (
								'type' => 'wildcard' 
						) 
				) 
		);
		
		// Template Path Stack
    	$templatePathStack[$dashedNamespace] = $this->_dir . '/view';
        
        // View manager configurations
        $viewManagerConfigurations = array(
            'display_not_found_reason' => true,
            'display_exceptions' => true,
            'doctype' => 'HTML5',
            'template_path_stack' => $templatePathStack
        );
        
        // Initialize Dependncy Injection as empty array
        $di = array();
        
        if ($this->_namespace == __NAMESPACE__) {
            
            $viewManagerConfigurations["strategies"] = array(
                'ViewJsonStrategy'
            );
            
            // declare di content only for once
            $di = array(
                'instance' => array(
                    // Acl Configuration with dependency injection
                    'Standard\Permissions\Event\Authentication' => array(
                        'parameters' => array(
                            'userAuthenticationPlugin' => 'Standard\Permissions\Authentication\Authentication'
                        )
                    )
                )
            );
        }
		
		$configArray = array (
				'di' => $di,
				// Doctrine Configurations
				'doctrine' => $doctrineConfiguration,
				
				// Invokable Controllers
				'controllers' => array (
						'invokables' => $invokables 
				),
				// Translator Options
				'translator' => $translator,
				
				// Route definition
				'router' => array (
						'routes' => $routes 
				),
				
				// View manager configurations
				'view_manager' => $viewManagerConfigurations 
		);
		
		// Check for custom configurations
		$customConfigArray = array();
        $fileModuleConfig = $this->_dir . '/config/module.config.php';
        if (file_exists($fileModuleConfig)) {
            // If custom configurations exists then get the configurations
            $customConfigArray = require_once $fileModuleConfig;
        }
        
        // Merge all the configurations
        $configArray = array_replace_recursive($configArray, (array) $customConfigArray);
		return $configArray;
	}
	
	/**
	 * Convert ABC to a-b-c AbcDef to abc-def
	 *
	 * @param string $string        	
	 * @return string
	 */
	private function convertToDash($string) {
		$new_string = strtolower ( $string [0] );
		for($i = 1; $i < strlen ( $string ); $i ++) {
			if (preg_match ( '/^[A-Z]$/', $string [$i] )) {
				$new_string = $new_string . "-" . strtolower ( $string [$i] );
			} else {
				$new_string = $new_string . $string [$i];
			}
		}
		return $new_string;
	}
	/**
	 * Load service configuration and factory settings accordingly
	 *
	 * @return unknown multitype:multitype:unknown
	 */
	public function getServiceConfig() {
		$serviceConfig = array();
        
        // Autoload General Function
        $standardFunctionPath = $this->_dir . '/src/' . $this->_namespace . '/Functions';
        
        if (is_dir($standardFunctionPath)) {
            $functions = scandir($standardFunctionPath);
            foreach ($functions as $function) {
                if (is_file($standardFunctionPath . DS . $function)) {
                    $key = $this->_namespace . "\\Functions\\" . str_replace(".php", "", $function);
                    $value = function  ($sm) use( $key)
                    {
                        $function = new $key();
                        return $function;
                    };
                    $serviceConfig[$key] = $value;
                }
            }
        }
        
        // Just add this once when this module is loaded
        if ($this->_namespace == __NAMESPACE__) {
            $serviceConfig['Standard\Permissions\Event\Authentication'] = function  ($sm)
            {
                $function = new \Standard\Permissions\Event\Authentication();
                return $function;
            };
        }
        return array(
            'factories' => $serviceConfig
        );
	}
	
    /**
	 * Autoloader configuration
	 *
	 * @return multitype:multitype:string
	 */
	public function getAutoloaderConfig() {
		// Get Standard autoload_classmap
		$fileAutoloadClassMap = $this->_dir . '/autoload_classmap.php';
        
        $autoloadClassMap = array();
        
        // Check if Autoload classmap for the module exists
        if (file_exists($fileAutoloadClassMap)) {
            
            // If Autoload classmap for the module exists
            $autoloadClassMap = require_once $fileAutoloadClassMap;
        }
        
        return array(
            'Zend\Loader\ClassMapAutoloader' => $autoloadClassMap,
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    $this->_namespace => $this->_dir . '/src/' . $this->_namespace
                )
            )
        );
	}
}

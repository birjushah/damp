<?php

namespace Standard\StaticOptions;

class StaticOptions {
    /**
     * 
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
	private static $_serviceLocator;
	private static $_user;
	private static $_em;
	private static $_base_url = null;
	
	private static $MYSQL_DATETIME_FORMAT = "Y-m-d H:i:s";
	private static $MYSQL_DATE_FORMAT = "Y-m-d";
	
	/** 
	 * Get the base url of the application
	 */
	public static function getBaseUrl(){
	    /*
		 *	@variable $uri Zend\Uri\Http
	     */
		if(static::$_base_url == null)
			static::$_base_url = self::getServiceLocator()->get('application')->getRequest()->getBasePath();
		
		return static::$_base_url;
	}
	/**
	 * Return the service locator
	 */
	public static function getServiceLocator() {
		return static::$_serviceLocator;
	}
	/**
	 * Set the service locator
	 *
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator        	
	 */
	public static function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
		static::$_serviceLocator = $serviceLocator;
	}
	/**
	 * Set the user model
	 *
	 * @param Entity $user        	
	 */
	public static function setCurrentUser($user) {
		static::$_user = $user;
	}
	/**
	 * Get the user model
	 */
	public static function getCurrentUser() {
		if (static::$_user == null) {
			static::setCurrentUser ( static::getGuestUserObject () );
		}
		return static::$_user;
	}
		
	/**
	 * Get doctrine metadata from classname
	 *
	 * @param string $className        	
	 * @throws \Exception
	 */
	public static function getClassMetaData($className) {
		if ($className == null) {
			throw new \Exception ( "Class name cannot be null, for getting metadata" );
		}
		return static::getEntityManager ()->getClassMetaData ( $className );
	}
	
	/**
	 * Get the doctrine entity manager
	 */
	public static function getEntityManager() {
		if (static::$_em == null) {
			static::$_em = static::getServiceLocator ()->get ( 'Doctrine\ORM\EntityManager' );
		}
		return static::$_em;
	}
	
	/**
	 * Return the guest user object
	 *
	 * @return \Standard\Entity\User $user
	 */
	public static function getGuestUserObject() {
		$config = static::getServiceLocator ()->get ( 'Config' );
		$userClass = "\\" . $config ['doctrine'] ['authenticationadapter'] ['odm_default'] ['identityClass'];
		$guestUser = new $userClass ();
		if(!$guestUser instanceof \Standard\Entity\User){
			throw new \Exception("{$userClass} should extend \Standard\Entity\User");
		}
		$guestUser->setUserId ( 0 );
		$guestUser->setGroupId ( 1 );
		$guestUser->setUsername ( \Standard\Permissions\Acl\Acl::$groups [1] );
		$guestUser->setGroupName ( \Standard\Permissions\Acl\Acl::$groups [1] );
		return $guestUser;
	}
	/**
	 * A shortcut method to get the configuration from config with seperator as
	 * ":"
	 * for example if there is some thing like
	 * invokables(array)->controllers(array)->data(array or end result)
	 * then getConfig(invokables:controllers:abcd); would fetch
	 * the end value of data
	 *
	 * @param string $configPath
	 * @throws \Exception
	 * @return Ambigous <unknown, object, multitype:>
	 */
	public static function getConfig ($configPath = "")
	{
		$config = self::getServiceLocator()->get('Config');
		if ($configPath == "") {
			return $config;
		}
		$arrayPath = explode(":", $configPath);
		$currentLocation = $config;
		foreach ($arrayPath as $path) {
			if (isset($currentLocation[$path])) {
				$currentLocation = $currentLocation[$path];
			} else {
				throw new \Exception("Path not found: " . $configPath);
			}
		}
		return $currentLocation;
	}
	
	/**
     * Send 403 Unauthorized response
     *
     * @return \Zend\Http\PhpEnvironment\Response
     */
    public static function get403Response ($error_code = "acl", $send = false)
    {
        if ($error_code == "db-acl") {
            $error_code = "error-db-row-level-acl";
        } else {
            $error_code = "error-acl";
        }
        // Get the initialized view helper manager from the service manager
        $viewHelperManager = self::getServiceLocator()->get('ViewHelperManager');
        
        // Get the template map from configurations
        $templateMap = self::getConfig("view_manager:template_map");
        
        // Generate a custom response
        $response = new \Zend\Http\PhpEnvironment\Response();
        $response->setStatusCode(403);
        
        // Generate an instance of view model to be rendered
        $model = new \Zend\View\Model\ViewModel();
        $model->setTemplate("layout/admin");
        
        $contentModel = new \Zend\View\Model\ViewModel();
        $contentModel->setTemplate('error/403');
        $contentModel->setVariables(array(
            'message' => "Forbidden",
            "reason" => (string) $error_code
        ));
        
        // Initialize the template map resolver
        $resolver = new \Zend\View\Resolver\TemplateMapResolver($templateMap);
        
        // Initialize the renderer
        $renderer = new \Zend\View\Renderer\PhpRenderer();
        
        // Set the Helper manager of the renderer with the helper initialized
        // with service locator
        $renderer->setHelperPluginManager($viewHelperManager);
        
        $renderer->setResolver($resolver);
        
        $contentOfModel = $renderer->render($contentModel);
        $model->setVariable("content", $contentOfModel);
        
        $content = $renderer->render($model);
        
        $response->setContent($content);
        if ($send) {
            $response->send();
            die();
        }
        return $response;
    }
	
	/**
     * Send 302 Permanant redirect response
     *
     * @return \Zend\Http\PhpEnvironment\Response
     */
    public static function get302Response ($redirectUrl = "/")
    {
    	$response = new \Zend\Http\PhpEnvironment\Response();
    	$response->getHeaders()->addHeaderLine('Location', self::getBaseUrl().$redirectUrl);
    	$response->send();
    	exit();
    } 
    
	public static function recursive_array_search ($needle, $haystack)
    {
        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value or (is_array($value) && self::recursive_array_search($needle, $value) !== false)) {
                return $current_key;
            }
        }
        return false;
    } 
    public static function generatePassword($length = 7) {
        $salt = "abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789";  // salt to select chars from (removed l and 1)
        srand(); // start the random generator
        $password = ""; // set the inital variable
        for ($i=0; $i < $length; $i++) { // loop and create password
            $password .= substr($salt, rand() % strlen($salt), 1);
        }
        return $password;
    }
}
<?php

namespace Standard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;

abstract class AbstractController extends AbstractActionController {
	/**
	 *
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $em;
	protected $_user;
	protected $acceptCriteria = array(
			'Zend\View\Model\ViewModel' => array(
					'text/html'
			),
			'Zend\View\Model\JsonModel' => array(
					'application/json'
			)
	);
	public function setEntityManager(EntityManager $em) {
		$this->em = $em;
	}
	public function getEntityManager() {
		if (null === $this->em) {
			$this->em = $this->getServiceLocator ()->get ( 'Doctrine\ORM\EntityManager' );
		}
		/*$this->em->getConnection()
				->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');*/
		return $this->em;
	}
	/**
	 * Get the functions defined in "Functions folder"
	 * 
	 * @param string $function_file_name
	 * @throws \Exception
	 * @return Ambigous <object, multitype:>
	 */
	public function getFunctions($function_file_name = ""){
		if((bool)$function_file_name) {
			return $this->getServiceLocator()->get($function_file_name);
		}
		throw new \Exception("File not found: '".$function_file_name."'");
	}
	
	public function getCurrentUser(){
	    if($this->_user == null){
	        $this->_user = \Standard\StaticOptions\StaticOptions::getCurrentUser();
	    }
	    return $this->_user;
	}
}
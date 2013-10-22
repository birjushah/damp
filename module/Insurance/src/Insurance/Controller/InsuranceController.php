<?php
namespace Insurance\Controller;

use Standard\Permissions\Acl\Acl;
use Standard\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Insurance\Entity\Insurance;
use Insurance\FormFilter\InsuranceFormFilter;
use Insurance\Form\InsuranceForm; 

class InsuranceController extends AbstractController{
	public function indexAction(){
	
	}
	public function addAction ()
	{
		$form = new InsuranceForm();
		$request = $this->getRequest();
		$form->setAttribute('action', $this->url()
				->fromRoute('insurance', array(
						'controller' => 'insurance',
						'action' => 'save'
				)));
		$view = new ViewModel(array(
				'addForm' => $form
		));
		$view->setVariable('pageTitle', 'Add Insurance Company');
		$view->setTemplate('insurance/insurance/add_edit');
		return $view;
	}
	public function gridAction ()
	{
		$request = $this->getRequest();
		$options = array(
				'column' => array(
						'query_result' => array(
								'actions'
						)
				)
		);
		$insurance = new Insurance();
		$response = $insurance->getGridData($request, $options);
		$aaData = &$response['aaData'];
		foreach ($aaData as $key => &$row) {
			$allData = $row[4];
			$bootstrapLinks = $this->BootstrapLinks();
			$row[4] = $bootstrapLinks->gridEditDelete($this->url()
					->fromRoute('insurance/wildcard', array(
							'controller' => 'insurance',
							'action' => 'edit',
							'id' => $allData['insurance_id']
					)),true, array('delete' => array('attributes' => array('delete-attr-id' => $allData['insurance_id']),'class' => array('delete btn-danger'))));
		}
		$response['aaData'] = array_values($aaData);
		return new JsonModel($response);
	}
	
	public function editAction ()
	{
		$user_id = (int)$this->params()->fromRoute('id');
		$em = $this->getEntityManager();
		$user = $em->getRepository('Insurance\Entity\Insurance')->findOneBy(array(
				'insurance_id' => $user_id
		));
		$form = new InsuranceForm();
		$form->setAttribute('action', $this->url()
				->fromRoute('insurance', array(
						'controller' => 'insurance',
						'action' => 'save'
				)));
		$formValues = $user->getArrayCopy();
		$form->populateValues($formValues);
		$view = new ViewModel(array(
				'addForm' => $form
		));
		$view->setVariable('pageTitle', 'Edit Company');
		$view->setTemplate('insurance/insurance/add_edit');
		return $view;
	}
	
	public function saveAction ()
	{
		$request = $this->getRequest();
		$formFilter = new InsuranceFormFilter();
		$form = new InsuranceForm();
		$form->setInputFilter($formFilter->getInputFilter());
		$redirectUrl = false;
		if ($request->isPost()) {
			$data = $request->getPost();
			$form->setData($data);
			if ($form->isValid()) {
				$data = $form->getData();
				$em = $this->getEntityManager();
				if ($data['insurance_id'] != "") {
					$insurance = $em->getRepository('Insurance\Entity\Insurance')->find($data['insurance_id']);
				} else {
					$insurance = new Insurance();
				}
				$insurance->exchangeArray($data);
				$em->persist($insurance);
				$em->flush();
				$response["success"] = true;
				$response["message"] = "Company updated successfully.";
				$redirectUrl = $this->url()->fromRoute('insurance', array(
						'controller' => 'insurance',
						'action' => 'index'
				));
			} else {
				$errorMessages = $form->getMessages();
				$formattedErrorMessages = "";
				foreach ($errorMessages as $keyElement => $errorMessage) {
					$errorText = array_pop($errorMessage);
					switch ($keyElement) {
						case 'name':
							$formattedErrorMessages .= "Email : $errorText<br />";
							break;
						default:
							$formattedErrorMessages .= "$keyElement : $errorText<br />";
							break;
					}
				}
				$response["message"] = $formattedErrorMessages;
			}
		}
		$viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
		$viewModel->setVariables(array(
				'registrationForm' => $form,
				'response' => $response,
				'redirect_url' => $redirectUrl
		));
		return $viewModel;
	}		public function deleteAction(){
		$request = $this->getRequest();
		$response = array();
		$response["success"] = false;
		if ($request->isPost()) {
	
			// Get the category id from post parameters
			$insurance_id = $request->getPost("insurance_id", false);
	
			if ($insurance_id) {
				// Get the entity manager
				$em = $this->getEntityManager();
				// Start Transaction
				$em->getConnection()->beginTransaction();
	
				try {
	
					$user = $em->getRepository('Insurance\Entity\Insurance')->findOneBy(array(
							"insurance_id" => $insurance_id
					));
	
					$em->remove($user);
					$em->flush();
	
					// Commit the changes
					$em->getConnection()->commit();
	
					$response["success"] = true;
				} catch (\Exception $ex) {
					$em->getConnection()->rollback();
					$em->close();
					$response['message'] = $ex->getTraceAsString();
				}
			}
		}
		return new JsonModel($response);
	}
}
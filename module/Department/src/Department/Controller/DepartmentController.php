<?php

namespace Department\Controller;



use Standard\Permissions\Acl\Acl;

use Standard\Controller\AbstractController;

use Zend\View\Model\ViewModel;

use Zend\View\Model\JsonModel;

use Department\Entity\Department;

use Department\FormFilter\DepartmentFormFilter;

use Department\Form\DepartmentForm; 



class DepartmentController extends AbstractController{

	public function indexAction(){

	

	}

	public function addAction ()

	{

		$form = new DepartmentForm();

		$request = $this->getRequest();

		$form->setAttribute('action', $this->url()

				->fromRoute('department', array(

						'controller' => 'department',

						'action' => 'save'

				)));

		$view = new ViewModel(array(

				'addForm' => $form

		));

		$view->setVariable('pageTitle', 'Add Department');

		$view->setTemplate('department/department/add_edit');

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

		$department = new Department();

		$response = $department->getGridData($request, $options);
		$aaData = &$response['aaData'];

		foreach ($aaData as $key => &$row) {
			$allData = $row[4];

			$bootstrapLinks = $this->BootstrapLinks();

			$row[4] = $bootstrapLinks->gridEditDelete($this->url()

					->fromRoute('department/wildcard', array(

							'controller' => 'department',

							'action' => 'edit',

							'id' => $allData['department_id']

					)),true, array('delete' => array('attributes' => array('delete-attr-id' => $allData['department_id']),'class' => array('delete btn-danger'))));

		}

		$response['aaData'] = array_values($aaData);

		return new JsonModel($response);

	}

	

	public function editAction ()

	{

		$user_id = (int)$this->params()->fromRoute('id');

		$em = $this->getEntityManager();

		$user = $em->getRepository('Department\Entity\Department')->findOneBy(array(

				'department_id' => $user_id

		));

		$form = new DepartmentForm();

		$form->setAttribute('action', $this->url()

				->fromRoute('department', array(

						'controller' => 'department',

						'action' => 'save'

				)));

		$formValues = $user->getArrayCopy();

		$form->populateValues($formValues);

		$view = new ViewModel(array(

				'addForm' => $form

		));

		$view->setVariable('pageTitle', 'Edit Department');

		$view->setTemplate('department/department/add_edit');

		return $view;

	}

	

	public function saveAction ()

	{

		$request = $this->getRequest();

		$formFilter = new DepartmentFormFilter();

		$form = new DepartmentForm();

		$form->setInputFilter($formFilter->getInputFilter());

		$redirectUrl = false;

		if ($request->isPost()) {

			$data = $request->getPost();

			$form->setData($data);

			if ($form->isValid()) {

				$data = $form->getData();

				$em = $this->getEntityManager();

				if ($data['department_id'] != "") {

					$department = $em->getRepository('Department\Entity\Department')->find($data['department_id']);

				} else {

					$department = new Department();

				}

				$department->exchangeArray($data);

				$em->persist($department);

				$em->flush();

				$response["success"] = true;

				$response["message"] = "Department updated successfully.";

				$redirectUrl = $this->url()->fromRoute('department', array(

						'controller' => 'department',

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

	}
	
	public function deleteAction(){
		$request = $this->getRequest();
		$response = array();
		$response["success"] = false;
		if ($request->isPost()) {
	
			// Get the category id from post parameters
			$department_id = $request->getPost("department_id", false);
	
			if ($department_id) {
				// Get the entity manager
				$em = $this->getEntityManager();
				// Start Transaction
				$em->getConnection()->beginTransaction();
	
				try {
	
					$user = $em->getRepository('Department\Entity\Department')->findOneBy(array(
							"department_id" => $department_id
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
<?php

namespace Builder\Controller;



use Standard\Permissions\Acl\Acl;

use Standard\Controller\AbstractController;

use Zend\View\Model\ViewModel;

use Zend\View\Model\JsonModel;

use Builder\Entity\Builder;

use Builder\FormFilter\BuilderFormFilter;

use Builder\Form\BuilderForm; 



class BuilderController extends AbstractController{

	public function indexAction(){

	

	}

	public function addAction ()

	{

		$form = new BuilderForm();

		$request = $this->getRequest();

		$form->setAttribute('action', $this->url()

				->fromRoute('builder', array(

						'controller' => 'builder',

						'action' => 'save'

				)));

		$view = new ViewModel(array(

				'addForm' => $form

		));

		$view->setVariable('pageTitle', 'Add Building');

		$view->setTemplate('builder/builder/add_edit');

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

		$builder = new Builder();

		$response = $builder->getGridData($request, $options);
		$aaData = &$response['aaData'];

		foreach ($aaData as $key => &$row) {
			$allData = $row[4];

			$bootstrapLinks = $this->BootstrapLinks();

			$row[4] = $bootstrapLinks->gridEditDelete($this->url()

					->fromRoute('builder/wildcard', array(

							'controller' => 'builder',

							'action' => 'edit',

							'id' => $allData['builder_id']

					)),true, array('delete' => array('attributes' => array('delete-attr-id' => $allData['builder_id']),'class' => array('delete btn-danger'))));
		}

		$response['aaData'] = array_values($aaData);

		return new JsonModel($response);

	}

	

	public function editAction ()

	{

		$user_id = (int)$this->params()->fromRoute('id');

		$em = $this->getEntityManager();

		$user = $em->getRepository('Builder\Entity\Builder')->findOneBy(array(

				'builder_id' => $user_id

		));

		$form = new BuilderForm();

		$form->setAttribute('action', $this->url()

				->fromRoute('builder', array(

						'controller' => 'builder',

						'action' => 'save'

				)));

		$formValues = $user->getArrayCopy();
		$structure = "";
		if($formValues['configuration'] != null){
			$structure = $formValues['configuration'];
		}
		$form->populateValues($formValues);

		$view = new ViewModel(array(

				'addForm' => $form,
				'structure' => $structure

		));

		$view->setVariable('pageTitle', 'Edit Builder');

		$view->setTemplate('builder/builder/add_edit');

		return $view;

	}

	

	public function saveAction ()

	{
		$request = $this->getRequest();

		$formFilter = new BuilderFormFilter();

		$form = new BuilderForm();

		$form->setInputFilter($formFilter->getInputFilter());

		$redirectUrl = false;

		if ($request->isPost()) {

			$data = $request->getPost();
			$form->setData($data);
			
			if ($form->isValid()) {
				$data = $form->getData();

				$em = $this->getEntityManager();

				if ($data['builder_id'] != "") {

					$builder = $em->getRepository('Builder\Entity\Builder')->find($data['builder_id']);

				} else {

					$builder = new Builder();

				}
				$builder->exchangeArray($data);

				$em->persist($builder);

				$em->flush();

				$response["success"] = true;

				$response["message"] = "Builder updated successfully.";

				$redirectUrl = $this->url()->fromRoute('builder', array(

						'controller' => 'builder',

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
			$builder_id = $request->getPost("builder_id", false);
	
			if ($builder_id) {
				// Get the entity manager
				$em = $this->getEntityManager();
				// Start Transaction
				$em->getConnection()->beginTransaction();
	
				try {
	
					$user = $em->getRepository('Builder\Entity\Builder')->findOneBy(array(
							"builder_id" => $builder_id
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
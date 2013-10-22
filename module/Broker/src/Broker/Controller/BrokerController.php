<?php

namespace Broker\Controller;



use Standard\Permissions\Acl\Acl;

use Standard\Controller\AbstractController;

use Zend\View\Model\ViewModel;

use Zend\View\Model\JsonModel;

use Broker\Entity\Broker;

use Broker\FormFilter\BrokerFormFilter;

use Broker\Form\BrokerForm; 



class BrokerController extends AbstractController{

	public function indexAction(){

	

	}

	public function addAction ()

	{

		$form = new BrokerForm();

		$request = $this->getRequest();

		$form->setAttribute('action', $this->url()

				->fromRoute('broker', array(

						'controller' => 'broker',

						'action' => 'save'

				)));

		$view = new ViewModel(array(

				'addForm' => $form

		));

		$view->setVariable('pageTitle', 'Add Broker Company');

		$view->setTemplate('broker/broker/add_edit');

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

		$broker = new Broker();

		$response = $broker->getGridData($request, $options);
		$aaData = &$response['aaData'];

		foreach ($aaData as $key => &$row) {
			$allData = $row[4];

			$bootstrapLinks = $this->BootstrapLinks();

			$row[4] = $bootstrapLinks->gridEditDelete($this->url()

					->fromRoute('broker/wildcard', array(

							'controller' => 'broker',

							'action' => 'edit',

							'id' => $allData['broker_id']

					)),true, array('delete' => array('attributes' => array('delete-attr-id' => $allData['broker_id']),'class' => array('delete btn-danger'))));
		}

		$response['aaData'] = array_values($aaData);

		return new JsonModel($response);

	}

	

	public function editAction ()

	{

		$user_id = (int)$this->params()->fromRoute('id');

		$em = $this->getEntityManager();

		$user = $em->getRepository('Broker\Entity\Broker')->findOneBy(array(

				'broker_id' => $user_id

		));

		$form = new BrokerForm();

		$form->setAttribute('action', $this->url()

				->fromRoute('broker', array(

						'controller' => 'broker',

						'action' => 'save'

				)));

		$formValues = $user->getArrayCopy();

		$form->populateValues($formValues);

		$view = new ViewModel(array(

				'addForm' => $form

		));

		$view->setVariable('pageTitle', 'Edit Broker');

		$view->setTemplate('broker/broker/add_edit');

		return $view;

	}

	

	public function saveAction ()

	{

		$request = $this->getRequest();

		$formFilter = new BrokerFormFilter();

		$form = new BrokerForm();

		$form->setInputFilter($formFilter->getInputFilter());

		$redirectUrl = false;

		if ($request->isPost()) {

			$data = $request->getPost();

			$form->setData($data);

			if ($form->isValid()) {

				$data = $form->getData();

				$em = $this->getEntityManager();

				if ($data['broker_id'] != "") {

					$broker = $em->getRepository('Broker\Entity\Broker')->find($data['broker_id']);

				} else {

					$broker = new Broker();

				}

				$broker->exchangeArray($data);

				$em->persist($broker);

				$em->flush();

				$response["success"] = true;

				$response["message"] = "Broker updated successfully.";

				$redirectUrl = $this->url()->fromRoute('broker', array(

						'controller' => 'broker',

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
			$broker_id = $request->getPost("broker_id", false);
	
			if ($broker_id) {
				// Get the entity manager
				$em = $this->getEntityManager();
				// Start Transaction
				$em->getConnection()->beginTransaction();
	
				try {
	
					$user = $em->getRepository('Broker\Entity\Broker')->findOneBy(array(
							"broker_id" => $broker_id
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
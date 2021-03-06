<?php

namespace Agent\Controller;



use Standard\Permissions\Acl\Acl;

use Standard\Controller\AbstractController;

use Zend\View\Model\ViewModel;

use Zend\View\Model\JsonModel;

use Agent\Entity\Agent;

use Agent\FormFilter\AgentFormFilter;

use Agent\Form\AgentForm; 



class AgentController extends AbstractController{

	public function indexAction(){

	

	}

	public function addAction ()

	{

		$form = new AgentForm();

		$request = $this->getRequest();

		$form->setAttribute('action', $this->url()

				->fromRoute('agent', array(

						'controller' => 'agent',

						'action' => 'save'

				)));

		$view = new ViewModel(array(

				'addForm' => $form

		));

		$view->setVariable('pageTitle', 'Add Agent');

		$view->setTemplate('agent/agent/add_edit');

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

		$agent = new Agent();

		$response = $agent->getGridData($request, $options);
		$aaData = &$response['aaData'];

		foreach ($aaData as $key => &$row) {
			$allData = $row[4];

			$bootstrapLinks = $this->BootstrapLinks();

			$row[4] = $bootstrapLinks->gridEditDelete($this->url()

					->fromRoute('agent/wildcard', array(

							'controller' => 'agent',

							'action' => 'edit',

							'id' => $allData['agent_id']

					)),true, array('delete' => array('attributes' => array('delete-attr-id' => $allData['agent_id']),'class' => array('delete btn-danger'))));

		}

		$response['aaData'] = array_values($aaData);

		return new JsonModel($response);

	}

	

	public function editAction ()

	{

		$user_id = (int)$this->params()->fromRoute('id');

		$em = $this->getEntityManager();

		$user = $em->getRepository('Agent\Entity\Agent')->findOneBy(array(

				'agent_id' => $user_id

		));

		$form = new AgentForm();

		$form->setAttribute('action', $this->url()

				->fromRoute('agent', array(

						'controller' => 'agent',

						'action' => 'save'

				)));

		$formValues = $user->getArrayCopy();

		$form->populateValues($formValues);

		$view = new ViewModel(array(

				'addForm' => $form

		));

		$view->setVariable('pageTitle', 'Edit Agent');

		$view->setTemplate('agent/agent/add_edit');

		return $view;

	}

	

	public function saveAction ()

	{

		$request = $this->getRequest();

		$formFilter = new AgentFormFilter();

		$form = new AgentForm();

		$form->setInputFilter($formFilter->getInputFilter());

		$redirectUrl = false;

		if ($request->isPost()) {

			$data = $request->getPost();

			$form->setData($data);

			if ($form->isValid()) {

				$data = $form->getData();

				$em = $this->getEntityManager();

				if ($data['agent_id'] != "") {

					$agent = $em->getRepository('Agent\Entity\Agent')->find($data['agent_id']);

				} else {

					$agent = new Agent();

				}

				$agent->exchangeArray($data);

				$em->persist($agent);

				$em->flush();

				$response["success"] = true;

				$response["message"] = "Agent updated successfully.";

				$redirectUrl = $this->url()->fromRoute('agent', array(

						'controller' => 'agent',

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
			$agent_id = $request->getPost("agent_id", false);
	
			if ($agent_id) {
				// Get the entity manager
				$em = $this->getEntityManager();
				// Start Transaction
				$em->getConnection()->beginTransaction();
	
				try {
	
					$user = $em->getRepository('Agent\Entity\Agent')->findOneBy(array(
							"agent_id" => $agent_id
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
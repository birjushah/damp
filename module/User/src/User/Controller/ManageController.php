<?php
namespace User\Controller;

use Standard\Permissions\Acl\Acl;
use Standard\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use User\Entity\User;
use User\FormFilter\ManageFormFilter;
use User\Form\ManageForm; 

class ManageController extends AbstractController{
	public function indexAction(){
		
	}
	
	public function addAction ()
	{
		$form = new ManageForm();
		$request = $this->getRequest();
		$form->setAttribute('action', $this->url()
				->fromRoute('user', array(
						'controller' => 'manage',
						'action' => 'save'
				)));
		$view = new ViewModel(array(
				'addForm' => $form
		));
		$view->setVariable('pageTitle', 'Add Manager');
		$view->setTemplate('user/manage/add_edit');
		return $view;
	}
	
	public function editAction ()
	{
		$user_id = (int)$this->params()->fromRoute('id');
		$em = $this->getEntityManager();
		$user = $em->getRepository('User\Entity\User')->findOneBy(array(
				'user_id' => $user_id
		));
		$form = new ManageForm();
		$form->setAttribute('action', $this->url()
				->fromRoute('user', array(
						'controller' => 'manage',
						'action' => 'save'
				)));
		$groupId = $user->getGroupId();
		$formValues = $user->getArrayCopy();
		$form->populateValues($formValues);
		$confirm = $form->get('confirm_password');
		$confirm->setValue($formValues['password']);
		$view = new ViewModel(array(
				'addForm' => $form
		));
		$view->setVariable('pageTitle', 'Edit Manager');
		$view->setTemplate('user/manager/add_edit');
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
		$groups = Acl::$groups;
		$user = new User();
		$response = $user->getGridData($request, $options);
		$aaData = &$response['aaData'];
		foreach ($aaData as $key => &$row) {
			//var_dump($row); die;
			$row[3] = ucwords(strtolower($groups[$row[3]]));
			$allData = $row[4];
			$bootstrapLinks = $this->BootstrapLinks();
			$row[4] = $bootstrapLinks->gridEditDelete($this->url()
					->fromRoute('user/wildcard', array(
							'controller' => 'manage',
							'action' => 'edit',
							'id' => $allData['user_id']
					)), false);
		}
		$response['aaData'] = array_values($aaData);
		return new JsonModel($response);
	}
	
	public function saveAction ()
	{
		$request = $this->getRequest();
		$formFilter = new ManageFormFilter();
		$form = new ManageForm();
		$form->setInputFilter($formFilter->getInputFilter());
		$redirectUrl = false;
		if ($request->isPost()) {
			$data = $request->getPost();
			$form->setData($data);
			if ($form->isValid()) {
				$data = $form->getData();
				$em = $this->getEntityManager();
				if ($data['user_id'] != "") {
					$user = $em->getRepository('User\Entity\User')->find($data['user_id']);
				} else {
					$user = new User();
				}
//				commented as not made sense at the time
// 				$group = $em->getRepository('Standard\Entity\Group')->findOneBy(array(
// 						'group_id' => 3
// 				));
				$user->setGroupId(3);
				$user->exchangeArray($data);
				$em->persist($user);
				$em->flush();
				$response["success"] = true;
				$response["message"] = "Manager updated successfully.";
				$redirectUrl = $this->url()->fromRoute('user', array(
						'controller' => 'manage',
						'action' => 'index'
				));
			} else {
				$errorMessages = $form->getMessages();
				$formattedErrorMessages = "";
				foreach ($errorMessages as $keyElement => $errorMessage) {
					$errorText = array_pop($errorMessage);
					switch ($keyElement) {
						case 'username':
							$formattedErrorMessages .= "Email : $errorText<br />";
							break;
						case 'confirm_password':
							$formattedErrorMessages .= "Confirm Password : $errorText<br />";
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
}
<?php



namespace Department\Form;



use Zend\Form\Form;

use Standard\StaticOptions\StaticOptions;



class DepartmentForm extends Form {

	public function __construct($name = null) {

		$name = $name == null ? "frm_manage" : $name;

		// we want to ignore the name passed

		parent::__construct ( $name );

		$this->setAttribute ( 'method', 'post' );

		$this->setAttributes ( array (

				'method' => 'post',

				'class' => 'form-horizontal' 

		) );

		$this->add ( array (

				'name' => 'department_id',

				'type' => 'hidden' 

		) );

		$this->add ( array (
				'name' => 'broker_id',
				'type' => 'select',
				'options' => array (
						'label' => false,
						'value_options' => $this->getbrokers()
				)
				,
				'attributes' => array (
						'required' => 'required',
						'style' => 'width:291px'
				)
		) );

		$this->add ( array (

				'name' => 'name',

				'attributes' => array (

						'type' => 'text',

						'placeholder' => '* Department Name',

						'class' => 'input-block-level',

						'required' => 'required' 

				),

				'options' => array (

						'label' => false 

				) 

		) );		

		$this->add ( array (

				'name' => 'submit',

				'attributes' => array (

						'type' => 'submit',

						'value' => 'Save',

						'id' => 'submitShipperButton',

						'class' => "btn btn-primary btn-large span2" 

				) 

		) );

	}
	
	private function getbrokers() {
		$em = StaticOptions::getEntityManager ();
		$qb = $em->createQueryBuilder ();
		$qb->select ( array (
				'b.broker_id',
				'b.name'
		) )->from ( 'Broker\Entity\Broker', 'b' );
		$query = $qb->getQuery ();
		$companies = $query->getArrayResult ();
	
		$options = array ();
		$options [""] = "-- Select Broker --";
		foreach ( $companies as $company ) {
			$options [$company ['broker_id']] = $company['name'];
		}
		return $options;
	}

}
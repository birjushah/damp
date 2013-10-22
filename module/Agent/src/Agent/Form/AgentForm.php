<?php



namespace Agent\Form;



use Zend\Form\Form;

use Standard\StaticOptions\StaticOptions;



class AgentForm extends Form {

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

				'name' => 'agent_id',

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

						'placeholder' => '* Agent Name',

						'class' => 'input-block-level',

						'required' => 'required' 

				),

				'options' => array (

						'label' => false 

				) 

		) );

		

		$this->add ( array (

				'name' => 'address',

				'attributes' => array (

						'type' => 'text',

						'placeholder' => '* Address',

						'class' => 'input-block-level',

						'required' => 'required'

				),

				'options' => array (

						'label' => false 

				) 

		) );

		

		$this->add ( array (

				'name' => 'phone',

				'attributes' => array (

						'type' => 'text',

						'placeholder' => 'Office Phone',

						'class' => 'input-block-level'

				),

				'options' => array (

						'label' => false

				)

		) );

		

		$this->add ( array (

				'name' => 'reference',

				'attributes' => array (

						'type' => 'text',

						'placeholder' => '* Reference',

						'class' => 'input-block-level',

						'required' => 'required'

				),

				'options' => array (

						'label' => false

				)

		) );

		

		$this->add ( array (

				'name' => 'email',

				'attributes' => array (

						'type' => 'email',

						'placeholder' => '* Email',

						'class' => 'input-block-level',

						'required' => 'required'

				),

				'options' => array (

						'label' => false

				)

		) );

		

		$this->add ( array (

				'name' => 'mobile',

				'attributes' => array (

						'type' => 'text',

						'placeholder' => '* Mobile',

						'class' => 'input-block-level',

						'required' => 'required'

				),

				'options' => array (

						'label' => false

				)

		) );

		

		$this->add ( array (

				'name' => 'latitude',

				'attributes' => array (

						'type' => 'text',

						'placeholder' => 'Latitude',

						'class' => 'input-block-level',

						'id' => 'latitude',

						'required' => 'required' 

				) 

		) );

		

		$this->add ( array (

				'name' => 'longitude',

				'attributes' => array (

						'type' => 'text',

						'placeholder' => 'Longitude',

						'id' => 'longitude',

						'class' => 'input-block-level'

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
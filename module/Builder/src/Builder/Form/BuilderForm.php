<?php



namespace Builder\Form;



use Zend\Form\Annotation\Attributes;

use Zend\Form\Form;

use Standard\StaticOptions\StaticOptions;



class BuilderForm extends Form {

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

				'name' => 'builder_id',

				'type' => 'hidden' 

		) );
		$this->add ( array (
		
				'name' => 'configuration',
		
				'type' => 'hidden'
		
		) );

		$this->add ( array (

				'name' => 'name',

				'attributes' => array (

						'type' => 'text',

						'placeholder' => '* Builder Name',

						'class' => 'input-block-level',

						'required' => 'required' 

				),

				'options' => array (

						'label' => false 

				) 

		) );

		$this->add ( array (
				'name' => 'type',
				'type' => 'select',
				'options' => array (
						'label' => false,
						'value_options' => array(
								'Commercial' => 'commercial',
								'Residential' => 'residential'
								)
				)
				,
				'attributes' => array (
						'required' => 'required',
						'style' => 'width:291px'
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
	
	private function getcompanies() {
		$em = StaticOptions::getEntityManager ();
		$qb = $em->createQueryBuilder ();
		$qb->select ( array (
				'i.insurance_id',
				'i.name'
		) )->from ( 'Insurance\Entity\Insurance', 'i' );
		$query = $qb->getQuery ();
		$companies = $query->getArrayResult ();
	
		$options = array ();
		$options [""] = "-- Select Insurance Company --";
		foreach ( $companies as $company ) {
			$options [$company ['insurance_id']] = $company['name'];
		}
		return $options;
	}

}
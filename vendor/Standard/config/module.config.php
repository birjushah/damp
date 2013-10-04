<?php
namespace Standard;

return array(
    'doctrine' => array(
        'authenticationadapter' => array(
            'odm_default' => array(
                'objectManager' => 'Doctrine\ORM\EntityManager',
                'identityClass' => 'Standard\Entity\User',
                'identityProperty' => 'email',
                'credentialProperty' => 'password'
            )
        )
    ),
    'acl' => array(
        'guard' => array(
            'User\Controller\Manage' => array(
                'all' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
            'Insurance\Controller\Insurance' => array(
                'all' => array(
                  	'groups' => array(
                    	'ADMINISTRATOR',
                  			'USER'
                    )
                )
            )
        )
    )
);
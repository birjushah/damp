<?php
namespace Standard;

return array(
    'doctrine' => array(
        'authenticationadapter' => array(
            'odm_default' => array(
                'objectManager' => 'Doctrine\ORM\EntityManager',
                'identityClass' => 'Standard\Entity\User',
                'identityProperty' => 'username',
                'credentialProperty' => 'password'
            )
        )
    ),
    'acl' => array(
        'guard' => array(
            'Admin\Controller\Index' => array(
                'all' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
			'Admin\Controller\User' => array(
                'all' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
        	'Admin\Controller\Employer' => array(
        			'all' => array(
        					'groups' => array(
        							'ADMINISTRATOR'
        					)
        			)
        	),
        	'Admin\Controller\JobCategory' => array(
        			'all' => array(
        					'groups' => array(
        							'ADMINISTRATOR'
        					)
        			)
        	),
        	'Admin\Controller\JobType' => array(
        			'all' => array(
        					'groups' => array(
        							'ADMINISTRATOR'
        					)
        			)
        	),
        	'Admin\Controller\JobRegion' => array(
        			'all' => array(
        					'groups' => array(
        							'ADMINISTRATOR'
        					)
        			)
        	),
        	'Admin\Controller\JobSalaryType' => array(
        			'all' => array(
        					'groups' => array(
        							'ADMINISTRATOR'
        					)
        			)
        	),
        	'Admin\Controller\JobTechnews' => array(
        			'all' => array(
        					'groups' => array(
        							'ADMINISTRATOR'
        					)
        			)
        	),
            'Admin\Controller\College' => array(
                'all' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
            'Admin\Controller\ExamDates' => array(
                'all' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
            'Admin\Controller\MembersReceiptGroup' => array(
                'all' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
            'Admin\Controller\MembersDepositsGroup' => array(
                'all' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
            'Admin\Controller\MembersItems' => array(
                'all' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
        	'Admin\Controller\EmployerType' => array(
        			'all' => array(
        					'groups' => array(
        							'ADMINISTRATOR'
        					)
        			)
        	),
        	'Admin\Controller\CmsContentCategory' => array(
        			'all' => array(
        					'groups' => array(
        							'ADMINISTRATOR'
        					)
        			)
        	),
        	'Admin\Controller\CmsContent' => array(
        			'all' => array(
        					'groups' => array(
        							'ADMINISTRATOR'
        					)
        			)
        	),
        	'Admin\Controller\CmsMenu' => array(
        			'all' => array(
        					'groups' => array(
        							'ADMINISTRATOR'
        					)
        			)
        	),
        	'Admin\Controller\CmsLink' => array(
        			'all' => array(
        					'groups' => array(
        							'ADMINISTRATOR'
        					)
        			)
        	),
            'Admin\Controller\Settings' => array(
                'all' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
            'Admin\Controller\Country' => array(
                'all' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
            'Admin\Controller\Members' => array(
                'all' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
			'Admin\Controller\Login' => array(
                'index' => array(
                    'groups' => array(
                        'GUEST'
                    )
                )
            ),
            'Admin\Controller\Logout' => array(
                'index' => array(
                    'groups' => array(
                        'ADMINISTRATOR'
                    )
                )
            ),
			'DoctrineORMModule\Yuml\YumlController' => array(
                'index' => array(
                		'groups' => array(
                				'GUEST',
                				'ADMINISTRATOR'
                		)
                )
            )
        ),
		'403-Redirect' => array(
            array(
                "resource" => 'Admin\Controller',
                "action" => "*",
                "redirect-group" => array(
                    "ADMINISTRATOR" => "/admin/login",
                    "USER" => "/admin/login",
					"GUEST" => "/admin/login",
                )
            )
        )
    )
);
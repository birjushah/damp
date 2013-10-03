<?php
return array (
        'navigation' => array (
                'default' => array (
                        array (
                                'label' => 'Login',
                                'route' => 'user',
                                'controller' => 'login',
                                'action' => 'index',
                                'resource' => 'User\Controller\Login',
                                'privilege' => 'index',
                                'order' => 1 
                        ),
                		array (
                				'label' => 'Manage',
                				'route' => 'user',
                				'controller' => 'manage',
                				'action' => 'index',
                				'resource' => 'User\Controller\Manage',
                				'privilege' => 'index',
                				'order' => 1,
                				'pages' => array (
                						array (
                								'label' => 'Add Manager',
                								'route' => 'user',
                								'controller' => 'manage',
                								'action' => 'add',
                								'resource' => 'User\Controller\Manage',
                								'privilege' => 'add',
                								'visible' => false
                						),
                						array (
                								'label' => 'Edit Manager',
                								'route' => 'user/wildcard',
                								'controller' => 'manage',
                								'action' => 'edit',
                								'resource' => 'User\Controller\Manage',
                								'privilege' => 'edit',
                								'visible' => false
                						)
                				)
                		),
                        array (
                                'label' => 'Insurance Company',
                                'route' => 'insurance',
                                'controller' => 'insurance',
                                'action' => 'index',
                                'resource' => 'Insurance\Controller\Insurance',
                                'privilege' => 'index',
                                'order' => 2,
                                'pages' => array (
                                        array (
            								'label' => 'Add Insurance Company',
            								'route' => 'insurance',
            								'controller' => 'insurance',
            								'action' => 'add',
            								'resource' => 'Insurance\Controller\Insurance',
            								'privilege' => 'add',
            								'visible' => false
                                        ),
                                        array (
            								'label' => 'Add Insurance Company',
            								'route' => 'insurance/wildcard',
            								'controller' => 'insurance',
            								'action' => 'edit',
            								'resource' => 'Insurance\Controller\Insurance',
            								'privilege' => 'edit',
            								'visible' => false
                                        )
                                )
                        )
                )
        ) 
);
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
                				'label' => 'Manage Users',
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
                						),
                						array (
                								'label' => 'Delete Manager',
                								'route' => 'user/wildcard',
                								'controller' => 'manage',
                								'action' => 'delete',
                								'resource' => 'User\Controller\Manage',
                								'privilege' => 'delete',
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
            								'label' => 'Edit Insurance Company',
            								'route' => 'insurance/wildcard',
            								'controller' => 'insurance',
            								'action' => 'edit',
            								'resource' => 'Insurance\Controller\Insurance',
            								'privilege' => 'edit',
            								'visible' => false
                                        )
                                )
                        ),
                		array (
                				'label' => 'Brokers',
                				'route' => 'broker',
                				'controller' => 'broker',
                				'action' => 'index',
                				'resource' => 'Broker\Controller\Broker',
                				'privilege' => 'index',
                				'order' => 3,
                				'pages' => array (
                						array (
                								'label' => 'Add Broker',
                								'route' => 'broker',
                								'controller' => 'broker',
                								'action' => 'add',
                								'resource' => 'Broker\Controller\Broker',
                								'privilege' => 'add',
                								'visible' => false
                						),
                						array (
                								'label' => 'Edit Broker',
                								'route' => 'broker/wildcard',
                								'controller' => 'broker',
                								'action' => 'edit',
                								'resource' => 'Broker\Controller\Broker',
                								'privilege' => 'edit',
                								'visible' => false
                						)
                				)
                		),
                		array (
                				'label' => 'Agent',
                				'route' => 'agent',
                				'controller' => 'agent',
                				'action' => 'index',
                				'resource' => 'Agent\Controller\Agent',
                				'privilege' => 'index',
                				'order' => 4,
                				'pages' => array (
                						array (
                								'label' => 'Add Agent',
                								'route' => 'agent',
                								'controller' => 'agent',
                								'action' => 'add',
                								'resource' => 'Agent\Controller\Agent',
                								'privilege' => 'add',
                								'visible' => false
                						),
                						array (
                								'label' => 'Edit Agent',
                								'route' => 'agent/wildcard',
                								'controller' => 'agent',
                								'action' => 'edit',
                								'resource' => 'Agent\Controller\Agent',
                								'privilege' => 'edit',
                								'visible' => false
                						)
                				)
                		),
                		array (
                				'label' => 'Department',
                				'route' => 'department',
                				'controller' => 'department',
                				'action' => 'index',
                				'resource' => 'Department\Controller\Department',
                				'privilege' => 'index',
                				'order' => 5,
                				'pages' => array (
                						array (
                								'label' => 'Add Department',
                								'route' => 'department',
                								'controller' => 'department',
                								'action' => 'add',
                								'resource' => 'Department\Controller\Department',
                								'privilege' => 'add',
                								'visible' => false
                						),
                						array (
                								'label' => 'Edit Department',
                								'route' => 'department/wildcard',
                								'controller' => 'department',
                								'action' => 'edit',
                								'resource' => 'Department\Controller\Department',
                								'privilege' => 'edit',
                								'visible' => false
                						)
                				)
                		),
                		array (
                				'label' => 'Buildings',
                				'route' => 'builder',
                				'controller' => 'builder',
                				'action' => 'index',
                				'resource' => 'Builder\Controller\Builder',
                				'privilege' => 'index',
                				'order' => 5,
                				'pages' => array (
                						array (
                								'label' => 'Add Builder',
                								'route' => 'builder',
                								'controller' => 'builder',
                								'action' => 'add',
                								'resource' => 'Builder\Controller\Builder',
                								'privilege' => 'add',
                								'visible' => false
                						),
                						array (
                								'label' => 'Edit Builder',
                								'route' => 'builder/wildcard',
                								'controller' => 'builder',
                								'action' => 'edit',
                								'resource' => 'Builder\Controller\Builder',
                								'privilege' => 'edit',
                								'visible' => false
                						)
                				)
                		)
                )
        ) 
);
<?php
return [
	'id' => 'app-test',
	'basePath' => dirname(__DIR__),
	'aliases' => [
		'api' => dirname(dirname(__DIR__)).'/src/api',
		'common/config' => __DIR__, // dirname(dirname(__DIR__)).'/vendor/inspirenmy/yii2-core/src/common/config',
		//'common/modules/moduleManager' => dirname(dirname(__DIR__)).'/vendor/inspirenmy/yii2-core/src/common/modules/moduleManager',
		'vendor' => dirname(dirname(__DIR__)).'/vendor',
		//'@common/migrations' => '@vendor/inspirenmy/yii2-core/src/common/migrations',
	],
    'components' => [
        'i18n' => [
            'translations' => [
                '*'=> [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath'=>'@common/messages',
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;port=3306;dbname=test_test',
            'username' => 'root',
            'password' => 'root',
            'tablePrefix' => '',
            'charset' => 'utf8',
        ],
        'moduleManager' => [
            'class' => 'ant\moduleManager\components\ModuleManager',
			'moduleAutoloadPaths' => [
				'@ant', 
				'@vendor/inspirenmy/ecommerce/src/common/modules', 
				'@vendor/inspirenmy/user/src/common/modules',
				'@vendor/inspirenmy/yii2-core/src/common/modules',
			],
        ],
        /*'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => [\ant\rbac\Role::ROLE_GUEST, \ant\rbac\Role::ROLE_USER],
        ],*/
        'user' => [
			'class' => 'yii\web\User',
            'identityClass' => 'ant\user\models\User',
        ],
	],
	'controllerMap' => [
		'module' => [
			'class' => 'ant\moduleManager\console\controllers\DefaultController',
		],
		'migrate' => [
			'class' => 'ant\moduleManager\console\controllers\MigrateController',
            'migrationPath' => [
                '@common/migrations/db',
                '@yii/rbac/migrations',
				'@tests/migrations/db',
            ],
            'migrationNamespaces' => [
                'yii\queue\db\migrations',
				'ant\moduleManager\migrations\db',
			],
            'migrationTable' => '{{%system_db_migration}}'
		],
		'rbac-migrate' => [
			'class' => 'ant\moduleManager\console\controllers\RbacMigrateController',
            'migrationPath' => [
                '@common/migrations/rbac',
            ],
            'migrationTable' => '{{%system_rbac_migration}}',
            'migrationNamespaces' => [
                'ant\moduleManager\migrations\rbac',
			],
            'templateFile' => '@common/rbac/views/migration.php'
		],
	],
];
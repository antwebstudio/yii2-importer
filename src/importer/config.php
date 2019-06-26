<?php

return [
    'id' => 'importer',
    'class' => ant\importer\Module::class,
    'isCoreModule' => false,
	'depends' => [],
	'modules' => [
		'backend' => [
			'class' => ant\importer\backend\Module::class,
		],
	],
];
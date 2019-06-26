<?php
/**
 * Yii console bootstrap file.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_LOCALHOST') or define('YII_LOCALHOST', true);

defined('YII_PROJECT_BASE_PATH') or define('YII_PROJECT_BASE_PATH', dirname(dirname(dirname(__DIR__))));
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(dirname(dirname(dirname(__DIR__))))));

require_once(YII_PROJECT_BASE_PATH . '/vendor/autoload.php');
//require_once(YII_APP_BASE_PATH . '/tests/bootstrap.php');
require_once(YII_PROJECT_BASE_PATH . '/vendor/yiisoft/yii2/Yii.php');
require_once(YII_APP_BASE_PATH . '/common/config/bootstrap.php');

$dotenv = new \Dotenv\Dotenv( YII_PROJECT_BASE_PATH.'/common/config' );
$dotenv->load();

Yii::setAlias('@tests', dirname(dirname(__DIR__)));
Yii::setAlias('@project', YII_PROJECT_BASE_PATH);

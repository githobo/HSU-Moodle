<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
'name'=>'Blog',

// preloading 'log' component
'preload'=>array('log'),

// autoloading model and component classes
'import'=>array(
'application.models.*',
'application.components.*',
),

// application components
'components'=>array(
'log'=>array(
'class'=>'CLogRouter',
'routes'=>array(
array(
'class'=>'CFileLogRoute',
'levels'=>'error, warning',
),
),
),
'user'=>array(
// enable cookie-based authentication
'allowAutoLogin'=>true,
),
// uncomment the following to set up database
'db'=>array(
'connectionString'=>'mysql:host=localhost;dbname=blog',
'username'=>'root',
'password'=>'',
),
),

// application-level parameters that can be accessed
// using Yii::app()->params['paramName']
'params'=>array(
// this is used in contact page
'adminEmail'=>'mrs15@humboldt.edu',
),
);
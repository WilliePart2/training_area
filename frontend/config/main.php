<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'mentor'
    ],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'index/index',
    'layout' => 'semantic_main',
    'components' => [
        'imageManager' => [
            'class' => 'frontend\components\ImageManager'
        ],
        'userManager' => [
            'class' => 'frontend\components\User'
        ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'frontend\models\Users',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_user-identity', 'httpOnly' => true],
        ],
        'mentor' => [
            'class' => 'yii\web\User',
            'identityClass' => 'frontend\models\Mentors',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_mentor-identity', 'httpOnly' => true]
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
            ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'mentor' => 'mentor/default/index'
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'db' => 'db'
        ],
    ],
    'modules' => [
        'mentor' => [
            'class' => 'frontend\modules\mentor\Mentor'
        ],
        'user' => [
            'class' => 'frontend\modules\user\User'
        ]
    ],
    'params' => $params,
];

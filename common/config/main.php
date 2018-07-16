<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset'
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\DbCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'db' => 'db'
        ]
    ],
    'modules' => [
        'class' => 'frontend\modules\mentor\Mentor'
    ]
];

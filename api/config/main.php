<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'language' => 'zh-CN',//默认语言
    'timeZone' => 'Asia/Shanghai',//默认时区
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
    ],
    'components' => [
        'sphinx' => [
            'class' => 'api\components\sphinx\BaseClas'
        ],
        'likeShare' => [
            'class' => 'api\components\likeRedis'
        ],
        'redisPage' => [
            'class' => 'api\components\redisPaging'
        ],
        'ali' => [
            'class' => 'api\components\alipay\Ali'
        ],
        'weChat' => [
            'class' => 'api\components\WX\weChat'
        ],
        'where' => [
            'class' => 'api\components\searchWhere'
        ],
        'request' => [
            'csrfParam' => '_csrf-api',
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the api
            'name' => 'advanced-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning']
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['login'],
                    'levels' => ['error', 'warning'],
                    'logVars' => ['*'],
                    'logFile' => '@runtime/logs/login.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['pay'],
                    'levels' => ['error', 'warning'],
                    'logVars' => ['*'],
                    'logFile' => '@runtime/logs/pay.log'
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
            'class' => 'api\components\Exception'
        ],
        'i18n' => [
            //多语言包设置
            'translations' => [
                '*' => [
                    'class' => yii\i18n\PhpMessageSource::className(),
                    'basePath' => '@api/messages',
                    'sourceLanguage' => 'zh-CN',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ]
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'POST v1/room-details' => 'v1/store/room-details',
                'POST v1/hotel-details' => 'v1/store/hotel-details',
                'POST v1/search-hotel' => 'v1/store/search-hotel',
                'POST v1/hotel-sort' => 'v1/store/hotel-sort',
                'POST v1/three-sort' => 'v1/store/three-sort',
                'POST v1/order-return' => 'v1/order-other/order-return',
                'POST v1/index-sort' => 'v1/index/sort-list',
                'POST v1/order-pay' => 'v1/order-other/order-pay',
                'POST v1/order-create' => 'v1/order-other/create-order',
                'GET v1/index-demo' => 'v1/demo/demo',
                'GET v1/demo' => 'v1/store/demo',
                'GET v1/store-img/<img_type:\w+>/<s_id:\d+>' => 'v1/store/store-img',
                'GET v1/pro-list/<pro_type:\w+>/<s_id:\d+>' => 'v1/product/pro-list',
                'GET v1/business-sort/<adCode\d+>/<top_sort:\d+>' => 'v1/common/sort-district',
                'GET v1/comments/<nav_type:\w+>/<page:\d+>/<s_id:\d+>' => 'v1/product/comment',
                'GET v1/pro-details' => 'v1/product/pro-details',
                'GET v1/store-details' => 'v1/store/store-details',
                'GET v1/synchro' => 'v1/common/synchro',
                'GET v1/refreshs' => 'v1/login/refresh',
                'POST v1/stores' => 'v1/store/index',
                'GET v1/users' => 'v1/user/index',
                'POST v1/like-share' => 'v1/common/store-like',
                'GET v1/nearbys' => 'v1/nearby/index',
                'GET v1/paymentCodes/<order_id:\w+>' => 'v1/order/payment-code',
                'GET v1/order-details/<order_id:\w+>' => 'v1/order/order-details',
                'GET v1/orders' => 'v1/order/index',
                'GET v1/likes' => 'v1/index/store-list',
                'GET v1/sends/<mobile:\d+>' => 'v1/common/sms-code',
                'GET v1/adverts/<mask:\w+>' => 'v1/common/advert',
                'POST v1/binds' => 'v1/login/bind-mobile',
                'POST v1/wxs' => 'v1/login/other-login',
                'GET v1/index/<id:\d+>' => 'v1/index/store-list',
                'GET v1/sites/<id:\d+>' => 'v1/site/index',
                'POST v1/sites' => 'v1/site/create',
                'POST v1/logins' => 'v1/login/create',
                "<module:\w+>/<controller:\w+>/<action:\w+>" => "<module>/<controller>/<action>",
            ]
        ],
    ],
    'params' => $params,
];

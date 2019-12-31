<?php

$api_config = [

    //不需要认证的操作
    'no_auth' => [
        'module' => [

        ],
        // 不需要认证的操作
        'action' => [
            'users/login'
        ],
        // 不需要认证的控制器
        'controller' => [],

    ],
    // APP 类型
    'app' => [
        0 => 'android',
        1 => 'ios',
        2 => 'weixin',
        3 => 'pc',
        9 => '其它'
    ],

    'auth' => [
        'token_key' => '',
        //过期时间:0不过期,时间单位秒
        'expire' => 0,
    ],

    //初始化应用自动执行
    "cron_on" => false, //是否开启
    "cron_time" => 60, //检查执行间隔
    "cron_config" => [
        //自主执行文件["goods_tj", "1 d", '2017-11-04 00:01:01'] 文件名 时间间隔 初始执行时间
    ]
];

/**
 *  开启刷新token功能
 */
\think\facade\Route::post('api/refresh_token', function () {
    api_refresh_token();
});

/**
 * 默认加载自动执行文件
 */
\think\facade\Hook::add('app_init','yiqiniu\\behavior\\CronRun');

return $api_config;
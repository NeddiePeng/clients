<?php
/**
 * 微信配置
 * User: Pengfan
 * Date: 2018/12/5
 * Time: 9:33
 */
return [

    //应用APP_ID
    'app_id' => '1231',

    //secret应用秘钥
    'secret' => '31231',

    //获取access_token
    'access_token_api' => 'https://api.weixin.qq.com/sns/oauth2/access_token',

    //刷新access_token
    'refresh_token' => 'https://api.weixin.qq.com/sns/oauth2/refresh_token',

    //校验token
    'check_token' => 'https://api.weixin.qq.com/sns/auth',

    //获取用户信息
    'getUser' => 'https://api.weixin.qq.com/sns/userinfo'
];
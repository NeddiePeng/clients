##客户端API
----

*接口返回状态code说明*
```text

200：成功

400：失败

0:数据为空

404：访问不存在

500: 服务器错误

```

```text
请求地址必须严格，格式不对返回404
```

*接口参数说明：*
```text
HeaderParam:header参数【登陆后:accessToken必须】

PostParam:post参数

GetParam:get参数直接跟在url后面
```
---

####获取手机验证码

*Url:http://www.caiji.com/v1/sends*

*Method:GET*

*Param:*
```text
mobile:手机号
```

*请求示例:http://www.caiji.com/v1/sends/15808278683*

*Return:*
```json
{
    "code": 200,
    "msg": "发送成功",
    "data": {}
}
```



####手机登录&注册

*Url:http://www.caiji.com/v1/login/index*

*Method:POST*

*Param:*
```text
mobile:手机号

sms_code:验证码
```

*Return:*
```json
{
    "code": 200,
    "msg": "登录成功",
    "data": {
        "accessToken": "2hYz3i5EjF0OKIfKEo8k_8MrEvg8-nEj_1543929520"
    }
}
```

*Field Descriptions*
```text
accessToken:授权码【后面请求一些接口时会用到】
```




####第三方登录【微信】

*Url:http://www.caiji.com/v1/wxs*

*Method:POST*

*Param:*
```text
code:换取access_token 的code
```

*Return:*
```json
{
    "code": 200,
    "msg": "授权成功",
    "data": {
        "accessToken" : "shncuibnasweui",
        "is_bind_mobile" : 1
    }
}
```

*Field Descriptions*
```text
accessToken:授权码【后面请求一些接口时会用到】

is_bind_mobile：是否已绑定手机号【1：是0：否】
```


####绑定手机号

*Url:http://www.caiji.com/v1/binds*

*Method:POST*

*PostParam:*
```text
mobile:手机号

sms_code:手机验证码
```

*HeaderParam:*
```text
accessToken:授权码
```

*Return:*
```json
{
    "code": 500,
    "msg": "Class 'Redis' not found",
    "data": {
        "file": "F:\\phpstudy\\PHPTutorial\\WWW\\clients\\api\\models\\LoginForm.php",
        "line": 53
    }
}
```




####advert数据

*Url:http://www.caiji.com/v1/adverts/mask*

*Method:GET*

*GetParam:*
```text
mask:advert显示位置  取值范围：【

    indexTop:首页顶部
    
    
    
】
```

*请求示例:http://www.caiji.com/v1/adverts/indexTop*

*Return:*
```json
{
    "code": 500,
    "msg": "Class 'Redis' not found",
    "data": {
        "file": "F:\\phpstudy\\PHPTutorial\\WWW\\clients\\api\\extend\\RedisCache.php",
        "line": 49
    }
}
```


####猜你喜欢

*Url:http://www.caiji.com/v1/likes/*

*Method:POST*

*GetParam:*
```text
lng:经度

lat:纬度

clients_id:设备唯一标识id
```

*HeaderParam:*
```text
accessToken:用户accessToken【登录的情况下】
```
*请求示例:http://www.caiji.com/v1/likes?lat=121.1&lng=12.21&clients_id=1231*

*Return:*
```json

```


####附近商家数据

*Url:http://www.caiji.com/v1/nearbys/*

*Method:GET*

*GetParam:*
```text
lng:经度

lat:纬度
```

*Return:*
```json

```








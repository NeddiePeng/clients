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

*Url:http://clients.qmwjj.cc/v1/sends*

*Method:GET*

*Param:*
```text
mobile:手机号
```

*请求示例:http://clients.qmwjj.cc/v1/sends/15808278683*

*Return:*
```json
{
    "code": 200,
    "msg": "发送成功",
    "data": {}
}
```



####手机登录&注册

*Url:http://clients.qmwjj.cc/v1/logins*

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


####刷新accessToken

*Url:http://clients.qmwjj.cc/v1/refreshs*

*Method:GET*

*HeaderParam:*
```text
accessToken:授权码
```

*Return；*
```json

```




####第三方登录【微信】

*Url:http://clients.qmwjj.cc/v1/wxs*

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

*Url:http://clients.qmwjj.cc/v1/binds*

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

*Url:http://clients.qmwjj.cc/v1/adverts/*

*Method:GET*

*GetParam:*
```text
mask:advert显示位置  取值范围：【

    indexTop:首页顶部
    
    nearbyTop:附近顶部
    
】
```

*请求示例:http://clients.qmwjj.cc/v1/adverts/indexTop*

*Return:*
```json
{
    "code": 200,
    "msg": "获取成功",
    "data": [
        {
            "id": "1",
            "img_url": "http://store.qmwjj.cc/storeImg/Atlas/20180919111437_209.png",
            "jump": "1",
            "type": "s"
        }
    ]
}
```

*Filed Descriptions*
```text
type:跳转类型【s:门店u:用户p:商品e:网页】  对应跳转字段jump
```


####首页门店列表

*Url:http://clients.qmwjj.cc/v1/likes/*

*Method:GET*

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
*请求示例:http://clients.qmwjj.cc/v1/likes?lat=34.265239&lng=108.943895&clients_id=523993e313b9ae16bad273d1ca74857d50a9b36b4991b73d6239270966c67d68*
```text
上面地址clients_id是ios的设备唯一标识
```
*Return:*
```json
{
    "code": 200,
    "msg": "获取成功",
    "data": [
        {
            "s_id": "9",//门店id
            "store_name": "测试门店",//门店名称
            "address": "详细地址",//门店地址
            "lat": "34.265502",//纬度
            "lng": "108.954347",//经度
            "headerImg": [//图册
                {
                    "img_url": "http://store.qmwjj.cc/storeImg/Atlas/20180919111437_209.png"
                },
                {
                    "img_url": "http://store.qmwjj.cc/storeImg/Atlas/20180919111437_381.jpg"
                },
                {
                    "img_url": "http://store.qmwjj.cc/storeImg/Atlas/20180922140543_622.jpg"
                }
            ],
            "sort_name": "餐饮",//分类名称
            "score": "0",//分数
            "type": "hotel",//类型【hotel:酒店other:其他】
            "like_num": 0,//点赞数
            "share_num": 0,//分享数
            "proData": [//产品
                {
                    "type": 1,//代金券
                    "name": ""//对应产品
                },
                {
                    "type": 2,//团购
                    "name": ""//如果没有就不显示
                },
                {
                    "type": 4,//活动
                    "name": ""//如果没有就不显示
                },
                {
                    "type": 3,//买单   
                    "name": ""//如果没有就不显示
                }
            ],
            "msgData": [//轮播信息
                "测试数据_1",
                "测试数据_2",
                "测试数据_3"
            ],
            "distance": "1km"//距离
        }
    ]
}
```


####附近商家数据

*Url:http://clients.qmwjj.cc/v1/nearbys/*

*Method:GET*

*GetParam:*
```text
lng:经度

lat:纬度

page:页码

sort_two：二级分类

sort_two字段说明：该字段包含  hot表示‘热门’   or  int类型的  分类数据id

top_sort:一级分类 

top_sort字段说明：该字段包含  all表示“全部”  或者 int类型的 分类数据id
```

*请求示例：http://clients.qmwjj.cc/v1/nearbys?lat=34.265239&lng=108.943895&clients_id=523993e313b9ae16bad273d1ca74857d50a9b36b4991b73d6239270966c67d68&sort_two=1&top_sort=1&page=1*

*Return:*
```json
{
    "code": 200,
    "msg": "获取成功",
    "data": [
        {
            "s_id": "24",//门店id
            "store_name": "测试门店",//门店名称
            "address": "陕西西安省政府大楼",//地址
            "lat": "34.264935",//纬度
            "lng": "108.953918",//经度
            "headerImg": {//门头图
                "img_url": "http://clients.qmwjj.cc/storeImg/Atlas/20180831103137_496.jpg"
            },
            "sort_name": "",//分类名称
            "score": "0",//分
            "type": "other",//门店类型
            "like_num": 0,
            "share_num": 0,
             "proData": [//产品
                            {
                                "type": 1,//代金券
                                "name": ""//对应产品
                            },
                            {
                                "type": 2,//团购
                                "name": ""//如果没有就不显示
                            },
                            {
                                "type": 4,//活动
                                "name": ""//如果没有就不显示
                            },
                            {
                                "type": 3,//买单   
                                "name": ""//如果没有就不显示
                            }
                        ],
            "msgData": [
                "测试数据_1",
                "测试数据_2",
                "测试数据_3"
            ],
            "distance": "0.9km"
        }
    ]
}
```


####门店顶赞 & 分享

*Url:http://clients.qmwjj.cc/v1/like-share*

*Method:POST*

*PostParam:*
```text
s_id:门店id

type:操作类型【like:点赞share：分享】
```

*HeaderParam:*
```text
accessToken:授权码
```

*Return:*
```json

```

####首页点击进入内页

*Url:http://clients.qmwjj.cc/v1/stores*

*Method:POST*

*PostParam:*
```text
top_sort:分类id

sort_two:二级分类id

lng:经度

lat：纬度

addr：地区   数据格式【{'addr_top':'hot','addr_two':12}】
            【当addr_top为hot标识热点地区为nearby标识附近；nearby的子集分别为 0，1，2，3，4，5】

          
```

*Return:*
```json
{
    "code": 200,
    "msg": "获取成功",
    "data": [
        {
            "s_id": "31",
            "store_name": "每一天便利店",
            "address": "大明宫万达广场1号门",
            "lat": "34.29292",
            "lng": "108.946825",
            "headerImg": false,
            "sort_name": "",
            "score": "0",
            "type": "other",
            "like_num": 0,
            "share_num": 0,
            "proData": [
                {
                    "type": 1,
                    "name": "代金券8代10,代金券9代10,代金券80代100,代金券80代100,代金券80代100,代金券80.00代100,代金券5代10,代金券2代10,代金券80代100,代金券150代200,代金券30代50,代金券11代50,代金券88代200,代金券44代100,代金券44.00代100,代金券44.00代200,代金券44.00代200,代金券44.00代200,代金券44.00代100,代金券5代50,代金券5代50,代金券5代50,代金券5代50,代金券44.00代200,代金券5.00代100,代金券44.00代100,代金券44.00代100,代金券8.00代200,代金券8.00代200,代金券8.00代200,代金券8888代200,代金券999代200,代金券999代200,代金券999代200,代金券99代200,代金券99代200,代金券80.00代100,代金券5.00代200,代金券44代100,代金券8.00代200,代金券30.00代200"
                },
                {
                    "type": 2,
                    "name": "肥牛火锅5.00元,香辣火锅串串香120.00元,头木木木欧诺欧诺模特扣女头目木偶剧女头木偶剧巨幕母女多头木木木木木木诺咯哈哈怕啥头部欧诺婆婆7轮婆婆流量监控哦看看咯坡头快就你借了阿拉车哦孙0哦李0DJ死了算了色就是色阿尔俄罗斯lesser俄罗斯1篇咯特略哭了太苦了特步阿福恶露230.00元,诺某某诺图谋哦哦align30.00元,哦哟一直送膜恶魔眼一岁李敏都无语77.00元,lz五突突突YY哦摩托木木木27.00元,阿鲁特卡卡头破噜噜噜80.00元,哦依稀贴膜哄哄你嘻嘻嘻嘻嗯呀智障一样450.00元,哦去我让学下去呜呜呜40.00元,哈哈哈哈80.00元,1111100.00元,这是车饰90.00元,测试套餐100.00元,仅售259！每一天便利店100减2098.00元,111.00元,团购123.00元,八人套12.00元,阿达团购656.00元,0021223.00元,一盘鲜花2.00元,Wang21.00元"
                },
                {
                    "type": 4,
                    "name": ""
                },
                {
                    "type": 3,
                    "name": ""
                }
            ],
            "msgData": [
                "测试数据_1",
                "测试数据_2",
                "测试数据_3"
            ],
            "distance": "3.1km"
        }
    ]
}
```


####订单

*Url:http://clients.qmwjj.cc/v1/orders*

*Method:GET*

*GetParam:*
```text
nav_type:订单类型【全部：all,0:待付款1:待使用2：待评价3：退款/售后】

sort_id：订单分类id
```

*HeaderParam:*
```text
accessToken:授权码
```

*Return:*
```json
{
    "code": 0,
    "msg": "暂没有订单数据",
    "data": {}
}
```



####订单详情

*Url:http://clients.qmwjj.cc/v1/order-details*

*Method:GET*

*GetParam:*
```text
order_id:订单号
```

*HeaderParam:*
```text
accessToken:授权码
```
*请求示例：http://clients.qmwjj.cc/v1/order-details/1231*

*Return:*
```json

```


####付款码

*Url:http://clients.qmwjj.cc/v1/paymentCodes/*

*Method:GET*

*GetParam:*
```text
order_id:订单号
```

*HeaderParam:*
```text
accessToken:授权码
```

*示例：http://clients.qmwjj.cc/v1/paymentCodes/212161*

*Return:*
```json

```

####个人中心

*Url:http://clients.qmwjj.cc/v1/users*

*Method:GET*

*HeaderParam:*
```text
accessToken:授权码
```

*Return：*
```json

```






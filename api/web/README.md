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


####商圈以及分类

*Url:http://clients.qmwjj.cc/v1/business-sort/*

*Method:GET*

*GetParam:*
```text
adCode:城市编码

top_sort：分类
```

*请求示例：http://clients.qmwjj.cc/v1/business-sort/610112001000/1  【参数顺序说明：adCode / top-sort】*

*Return:*
```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "addrData": [
            {
                "id": "2810",
                "areaName": "新城区",
                "center": "108.960716,34.266447",
                "lng": "108.960716",
                "lat": "34.266447",
                "businessData": [
                    {
                        "areaName": "西一路",
                        "lng": "108.9549956388207",
                        "lat": "34.261926793611785",
                        "center": "108.9549956388207,34.261926793611785",
                        "count": 2,
                        "id": 0
                    },
                    {
                        "areaName": "解放路",
                        "lng": "108.96314609007828",
                        "lat": "34.26720004308092",
                        "center": "108.96314609007828,34.26720004308092",
                        "count": 2,
                        "id": 0
                    },
                    {
                        "areaName": "尚勤路",
                        "lng": "108.96644391036418",
                        "lat": "34.26705222689076",
                        "center": "108.96644391036418,34.26705222689076",
                        "count": 2,
                        "id": 0
                    }
                ]
            }
        ],
        "sortData": [
            {
                "id": "255",
                "sort_name": "食品保健",
                "count": "0"
            }
        ]
    }
}
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
                    "name": "代金券30.00代200",
                    "now": 50,
                    "old": 100
                },
                {
                    "type": 2,
                    "name": ",一盘鲜花2.00元,Wang21.00元",
                    "now": 50,
                    "old": 100
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



####门店详情

*Url:http://clients.qmwjj.cc/v1/store-details*

*Method:GET*

*GetParam:*
```text
lat:纬度

lng:经度

s_id:门店id
```
*Return:*
```json
{
    "code": 200,
    "msg": "获取成功",
    "data": {
        "infoData": {
            "s_id": "31",
            "store_name": "每一天便利店",//门店名称
            "score": "0",//分数
            "sort_name": "",//分类名
            "per_capita": 100,//人均
            "mobile": "15029958140",//电话
            "do_business_time": "营业中:18:06-18:06",
            "address": "大明宫万达广场1号门",//地址
            "Notice": "欢迎光临！",//公告
            "headerImgData": {//图片数据
                "imgUrl": "http://store.qmwjj.cc/storeImg/Atlas/20180904170144_977.jpg",
                "count": 3
            },
            "distance": "3.1km"//距离
        },
        "proData": {
            "check": null,//买单数据
            "vouchers": [//代金券数据
                {
                    "name": "代金券8代10",
                    "price": "8.00",
                    "buy_num": "0",
                    "old_price": "10",
                    "rules": {
                        "overlaying": ""
                    },
                    "id": "74"
                }
            ],
            "group": [//团购数据
                {
                    "name": "2人肥牛火锅",
                    "price": "5.00",
                    "buy_num": "0",
                    "old_price": "12.00",
                    "rules": [],
                    "id": "7",
                    "headerImg": "http://store.qmwjj.cc/storeImg/Group_img/20181008102321_504.jpg"
                }
            ]
        }
    }
}
```



####门店相册

*Url:http://clients.qmwjj.cc/v1/store-img/*

*Method:GET*

*GetParam:*
```text
img_type:图片类型【all:全部  pro：商品  scen：环境  license：资质】

s_id:门店id
```

*请求示例：http://clients.qmwjj.cc/v1/store-img/all/31  【参数顺序  img_type / s_id】*

*Return:*
```json
{
    "code": 200,
    "msg": "获取成功",
    "data": {
        "imgList": [
            "http://store.qmwjj.cc/storeImg/Atlas/20180904170144_977.jpg",
            "http://store.qmwjj.cc/storeImg/Atlas/20181024105655_263.jpg",
            "http://store.qmwjj.cc/storeImg/Atlas/20181024105655_571.jpg",
            "http://store.qmwjj.cc/storeImg/Atlas/20181024142448_171.jpg",
            "http://store.qmwjj.cc/storeImg/Atlas/20181128095621_820.jpg",
            "http://store.qmwjj.cc/storeImg/License/20180930100303_887.jpg",
            "http://store.qmwjj.cc/storeImg/License/20180930100304_461.jpg"
        ]
    }
}
```


####门店商品数据

*Url:http://clients.qmwjj.cc/v1/pro-list/*

*Method:GET*

*GetParam:*
```text
pro_type：商品类型【1：代金券2：团购4：购物车】

s_id:门店id
```

*请求示例：http://clients.qmwjj.cc/v1/pro-list/2/31   【参数顺序：pro_type / s_id】*

*Return:*
```json
{
    "code": 200,
    "msg": "获取成功",
    "data": [
        {
            "id": "7",
            "x_id": "31",
            "project_id": "7",
            "type": "2",
            "group_name": "肥牛火锅",
            "img_url": "http://store.qmwjj.cc/storeImg/Group_img/20181008102321_504.jpg",
            "one_sort": "5",
            "two_sort": "7",
            "price": "12.00",
            "group_price": "5.00",
            "use_min": "2",
            "use_max": "2",
            "group_type": "周一,周二",
            "see_num": "0",
            "pay_num": "0",
            "description": "",
            "create_time": "1538965428",
            "up_time": "1539359999",
            "down_time": "1540655999",
            "status": "1"
        }
    ]
}
```


####商品详情center数据

*Url:http://clients.qmwjj.cc/v1/pro-details*

*Method:GET*

*GetParam:*
```text
s_id:门店id

id:商品id

pro_type:商品类型【1：代金券2：团购3：买单】
```

*请求示例：http://clients.qmwjj.cc/v1/pro-details?s_id=31&id=10&pro_type=1*

*Return:*
```json
{
    "code": 200,
    "msg": "获取成功",
    "data": {
        "topData": {
            "overlying": "叠加使用限制",
            "bespeak": "需提前预约",
            "refund": "随时退",
            "id": "10",//商品id
            "imgUrl": {//如果是代金券该数据不会返回
                "imgList": [
                    {
                        "img": "http://store.qmwjj.cc/storeImg/Dishes/20181008174841_576.jpg",
                        "name": "红烧鸡块好自为之"
                    }
                ],
                "count": 8
            },
            "groupContent": [//如果是代金券该数据不会返回
                {
                    "sort_name": "团购产品",
                    "selectType": "全部可用",
                    "content": [
                        {
                            "name": "红烧鸡块好自为之",
                            "num": "5",
                            "price": "70.40"
                        }
                    ]
                }
            ]
        },
        "rulesData": {
            "validityTime": "2018.10.10至2018.10.12",
            "unavailableTime": "法定节假日不可用",
            "useTime": "",
            "useRules": [
                "需提前预约",
                "使用叠加限制",
                "支持找零",
                "全场通用",
                "不提供发票",
                ""
            ]
        }
    }
}
```


####门店评论

*Url:*

*Method:GET*

*GetParam:*
```text
page:页码

s_id:门店id

nav_type:评论类型【all:全部,img:晒图,nice：好评,bed：差评】
```

*请求示例：http://clients.qmwjj.cc/v1/comments/img/1/31   【地址说明：参数顺序依次为nav_type / page / s_id】*

*Return:*
```json
{
    "code": 200,
    "msg": "获取成功",
    "data": [
        {
            "id": "1",//评论id
            "pro_id": "10",
            "type": "2",
            "time": "2018.08.17",//评论时间
            //评论内容
            "comment_text": "这是测试评论这是测试评论这是测试评论这是测试评论这是测试评论,这是测试评论,这是测试评论,这是测试评论,这是测试评论,这是测试评论,这是测试评论,这是测试评论",
            "Star_num": "1",//分数
            "userData": {//用户西悉尼
                "nickname": "月亮瓦",
                "header_img": "http://store.qmwjj.cc/storeImg/Dishes/20180810094333_829.jpg"
            },
            "is_be_reply": "1",
            "imgData": [//图片信息
                {
                    "id": "3",
                    "comment_id": "1",
                    "img_url": "http://store.qmwjj.cc/storeImg/Dishes/20180810094333_829.jpg",
                    "status": "1"
                }
            ],
            "replyData": [//回复数据
                {
                    "nickname": "商家回复",
                    "comment_text": "这是商家回复",
                    "time": "2018.08.17"
                }
            ],
            "proName": "诺某某诺图谋哦哦align"//评论商品
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






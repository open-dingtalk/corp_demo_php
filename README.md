本文档将带你一步步创建完成一个钉钉PHP企业应用，并可以在手机上体验该企业应用的实际效果，看到定制化的企业主页。[下载源码](https://github.com/open-dingtalk/corp-demo-php)

<font color=red>注意！注意！注意！demo中的数据存储一定要修改为mysql等持久化存储。</font>

# 项目部署

一、将工程clone到本地：```git clone https://github.com/open-dingtalk/corp-demo-php```，导入到IDE中，比如eclipse点击```File->import```导入到eclipse中

二、在企业[OA后台](https://oa.dingtalk.com/contacts.htm#/contacts?_k=ipju0m)【企业应用-工作台】点击进入开发者后台，获取CorpID与CorpSecret

<img src="https://img.alicdn.com/tfs/TB1nAhlSFXXXXbEapXXXXXXXXXX-1079-743.png">
<img src="https://img.alicdn.com/tfs/TB1VnlrSpXXXXXsaFXXXXXXXXXX-1276-251.png">

三、将CORP_ID 和 SECRET 配置在config.php 中

```
define("CORPID", "");
define("SECRET", "");
```

四、部署工程（apache或者Nginx）

五、在企业OA后台【企业应用-应用管理】里面创建微应用，并把工程的首页地址（例如：http://xx.xx.xx.x/index.html）填到微应用首页中并保存。[如何创建微应用？](https://open-doc.dingtalk.com/docs/doc.htm?spm=a219a.7629140.0.0.hlq1Vw&treeId=367&articleId=104938&docType=1)。 该demo只能在移动端才会有效果。（注意：移动端点击应用跳转，如果无法访问你填写的首页地址，请确保你服务端与手机端的网络在同一个网段）

<img src="https://img.alicdn.com/tfs/TB1_AJgRpXXXXX1aXXXXXXXXXXX-593-757.png">


六、打开钉钉手机客户端，打开工作面板，切换到自己上面创建微应用的企业，在工作面板上打开自己创建的微应用，可以看到如下界面，企业应用开发完成。

<img src="https://img.alicdn.com/tfs/TB1TMrcahrI8KJjy0FpXXb5hVXa-750-1378.png">

# 项目代码结构解释

1.项目主入口:index.html

2.前端免登鉴权逻辑:public/javascripts/app.js

3.部门管理:api/Department.php

4.消息管理:api/Message.php

5.员工管理:api/User.php

6.加解密库:crypto // 用来做回调消息验证

7.缓存:util/Cache.php

8.HTTP请求包装:util/HTTP.php

9.日志:util/Log.php

10.配置文件:config.php

11.通讯录变更回调接收:callback.php

12.企业应用主页使用到的接口:getOapiByName.php

#DEMO中的免登授权流程

1、在企业管理后台：https://oa.dingtalk.com/上注册完成之后，获取企业的corpId和CorpSecret可以参考以下步骤：OA管理后台-微应用-工作台(仅企业主管理员与子管员可查看)。可参考文档[获取企业相关开发信息](https://open-doc.dingtalk.com/docs/doc.htm?spm=a219a.7629140.0.0.zWOVqL&treeId=385&articleId=106926&docType=1#s1)。

2、通过调用[获取access_token的接口](https://open-doc.dingtalk.com/docs/doc.htm?spm=a219a.7629140.0.0.JB3OID&treeId=385&articleId=104980&docType=1#s2)获取企业的access_token。

请求说明

Https请求方式: GET
`https://oapi.dingtalk.com/gettoken?corpid=id&corpsecret=secrect`

参数说明

参数 | 参数类型 | 必须 | 说明
---------- | ------- | ------- | ------
corpid | String | 是 | 企业Id
corpsecret | String | 是 | 企业应用的凭证密钥

返回说明

a)正确的Json返回结果:

```
{
    "errcode": 0,
    "errmsg": "ok",
    "access_token": "fw8ef8we8f76e6f7s8df8s"
}
```

参数 | 说明
---- | -----
errcode | 错误码
errmsg | 错误信息
access_token | 获取到的凭证

3、通过调用[获取jsticket的接口](https://open-doc.dingtalk.com/docs/doc.htm?spm=a219a.7629140.0.0.Fg12Ak&treeId=385&articleId=104966&docType=1)获取企业的jsticket。

请求说明
Https请求方式：GET

`https://oapi.dingtalk.com/get_jsapi_ticket?access_token=ACCESS_TOKE`

参数说明

参数 | 参数类型 | 必须 | 说明
---------- | ------- | ------- | ------
access_token | String | 是 | 调用接口凭证
type | String | 是 | 这里是固定值，jsapi

返回结果
正确时返回示例如下：

```
{
    "errcode": 0,
    "errmsg": "ok",
    "ticket": "dsf8sdf87sd7f87sd8v8ds0vs09dvu09sd8vy87dsv87",
    "expires_in": 7200
}
```

参数 | 说明
---------- | ------
errcode | 错误码
errmsg | 错误信息
ticket | 用于JS API的临时票据
expires_in | 票据过期时间

4、在后端通过sign(ticket, nonceStr, timeStamp, url)计算前端校验需要使用的签名信息。

```php
public function sign($ticket, $nonceStr, $timeStamp, $url)
    {
        $plain = 'jsapi_ticket=' . $ticket .
            '&noncestr=' . $nonceStr .
            '&timestamp=' . $timeStamp .
            '&url=' . $url;
        return sha1($plain);
    }
```
5、将：'url'，'nonceStr'，'agentId'，'timeStamp'，'corpId'，'signature'传递到前端页面。
```javascript
{
    " jsticket": "xxx",
    "signature": "xxx",
    "nonceStr": "xxx",
    "timeStamp": "xxx",
    "corpId": "xxx",
    "agentid": "" // 企业自建应用，agentId可以不传
}
```

6、在前端H5页面引入jsapi

[https://g.alicdn.com/dingding/open-develop/1.6.9/dingtalk.js](https://g.alicdn.com/dingding/open-develop/1.6.9/dingtalk.js)

或者

[http://g.alicdn.com/dingding/open-develop/1.6.9/dingtalk.js](http://g.alicdn.com/dingding/open-develop/1.6.9/dingtalk.js)

使用jsapi前需先确认jsapi是否需鉴权，请查看[移动端jsapi总览](https://open-doc.dingtalk.com/docs/doc.htm?spm=a219a.7629140.0.0.TnSROX&treeId=171&articleId=106834&docType=1)。若需鉴权，需使用jsapi提供的dd.config（PC客户端使用DingTalkPC.config）接口进行签名校验。【注意：需鉴权的jsapi，需先进行dd.config注册然后再在dd.ready里面调用jsAPI】

```javacsript
dd.config({
    agentId: _config.agentId, // 服务端传来的congfig信息
    corpId: _config.corpId,
    timeStamp: _config.timeStamp,
    nonceStr: _config.nonceStr,
    signature: _config.signature,
    jsApiList: [ // 所有需要使用到的jsApi需要在config的时候进行注册，写在这里。
        'runtime.info',
        'biz.user.get',
        'biz.contact.choose',
        'biz.telephone.call',
        'biz.ding.post']
});
```

7、使用钉钉js-api提供的[获取免登授权码](https://open-doc.dingtalk.com/docs/doc.htm?spm=a219a.7629140.0.0.bTDGlN&treeId=369&articleId=104911&docType=1#s1)接口获取CODE，此jsapi无需鉴权（即不需要进行dd.config）

```javascript
dd.ready(function() {
	dd.runtime.permission.requestAuthCode({
		corpId: "corpid",
	    onSuccess: function(result) {
	    /*{
	        code: 'hYLK98jkf0m' //string authCode
	    }*/
	    },
	    onFail : function(err) {}

	});
});
```
参数说明

参数 | 参数类型 | 必须 | 说明
----- | ------- | ------- | ------
corpId | String | 是 | 企业ID

返回说明

参数 | 说明
---- | -----
code | 授权码

8、[通过CODE换取用户身份](https://open-doc.dingtalk.com/docs/doc.htm?spm=a219a.7629140.0.0.iU4u4U&treeId=385&articleId=104969&docType=1#s0)

企业应用的服务器在拿到CODE后，需要将CODE发送到钉钉开放平台接口，如果验证通过，则返回CODE对应的用户信息. **此接口只用于免登服务中用来换取用户信息** 

请求说明

Https请求方式: GET

`https://oapi.dingtalk.com/user/getuserinfo?access_token=ACCESS_TOKEN&code=CODE`

参数说明

参数 | 参数类型 | 必须 | 说明
---------- | ------- | ------- | ------
access_token | String | 是 | 调用接口凭证
code | String | 是 | requestAuthCode接口中获取的CODE

返回结果

正确时返回示例如下：

```
{
    "errcode": 0,
    "errmsg": "ok",
    "userid": "USERID",
    "deviceId":"DEVICEID",
    "is_sys": true,
    "sys_level": 0|1|2
}
```

9 、通过userId获取用户详情

请求说明

Https请求方式: GET

`https://oapi.dingtalk.com/user/get?access_token=ACCESS_TOKEN&userid=zhangsan`

参数 | 参数类型 | 必须 | 说明
---------- | ------- | ------- | ------
access_token | String | 是 | 调用接口凭证
userid | String |是 | 员工在企业内的UserID，企业用来唯一标识用户的字段。
lang | String | 否 | 通讯录语言(默认zh_CN另外支持en_US)

返回结果

```
{
    "errcode": 0,
    "errmsg": "ok",
    "userid": "zhangsan",
    "name": "张三",
    "tel" : "010-123333",
    "workPlace" :"",
    "remark" : "",
    "mobile" : "13800000000",
    "email" : "dingding@aliyun.com",
    "active" : true,
    "orderInDepts" : "{1:10, 2:20}",
    "isAdmin" : false,
    "isBoss" : false,
    "dingId" : "WsUDaq7DCVIHc6z1GAsYDSA",
    "unionid" : "cdInjDaq78sHYHc6z1gsz",
    "isLeaderInDepts" : "{1:true, 2:false}",
    "isHide" : false,
    "department": [1, 2],
    "position": "工程师",
    "avatar": "dingtalk.com/abc.jpg",
    "jobnumber": "111111",
    "extattr": {
                "爱好":"旅游",
                "年龄":"24"
                }
}
```

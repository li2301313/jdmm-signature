## 本组件只由一个核心计算方法组层成
## 参数说明
* $date 请求时间。示例：Wed, 05 Sep. 2012 23:00:00 GMT   ，不强制要求时间格式，只是当做字符串处理
* $path 请求路径。示例：/resource/audit
* $query query参数。示例：id=110&name=testName
* $method 请求方式。示例：POST
* $body 主体内容。json格式
* $SecretKey 用户密钥。公共服务数据库（jd_common）的jd_api_auth表查询出请求用户的密钥access_key_secret，公共服务提供了RPC查询接口searchSecret()。
* $signature 用户提供的签名

##使用说明
###中间件内调用
中间件

## 加密说明
### Header头里要传递的数据

| 名称          | 类型   | 说明                                                         |
| :------------ | :----- | :----------------------------------------------------------- |
| Authorization | 字符串 | 用于验证请求合法性的认证信息。                               |
| Date          | 字符串 | HTTP 1.1协议中规定的GMT时间，例如：Wed, 05 Sep. 2012 23:00:00 GMT |



### 组织参与签名计算的字符串

```
Authorization = GroupName + ":" + AccessKeyId + ":" + Signature
Signature = base64(hmac-sha1(AccessKeySecret,
            VERB + "\n"
            + Content-MD5 + "\n"
            + Date + "\n"
			+ Url))
```

当`Body`部分为空时Signature的签名算法

```
Signature = base64(hmac-sha1(AccessKeySecret,
            VERB + "\n"
            + "\n"
            + Date + "\n"
			+ Url))
```

#### GroupName

由平台分配

| GroupName | 描述    |
| --------- | ------- |
| DSP       | dsp业务 |


#### AccessKeyId

调用方的唯一身份标识，由平台分配

#### AccessKeySecret

密钥，由平台分配

#### VERB

表示HTTP请求的Method，主要有PUT、GET、POST、HEAD、DELETE等，`统一为全大写格式`

#### Content-MD5

Content-MD5 是指 Body 的 MD5 值，只有当 Body 非 Form 表单时才计算 MD5,并转换成小写形式，当body部分为空时，以"\n"代替，计算方式为：

java:

`String content-MD5 = MD5(bodyStream.getbytes("UTF-8")).toLowerCase();`

php: 

`$contentMd5 = strtolower(md5(file_get_contents('php://input')));`

swoft: 

`$data = $request->raw();`

#### Url

Url 指 Path + Query 中 Form 参数，组织方法：对 Query 参数按照字典对 Key 进行排序后按照如下方法拼接，如果 Query 参数为空，则 Url = Path，不需要添加 ？，如果某个参数的 Value 为空只保留 Key 参与签名，等号不需要再加入签名。

```
String url =
Path +
"?" +
Key1 + "=" + Value1 +
"&amp;" + Key2 + "=" + Value2 +
...
"&amp;" + KeyN + "=" + ValueN
```
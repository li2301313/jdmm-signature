<?php

namespace Jdmm\Signature;


/**
 * Class SignLogic
 * @package Jdmm\Signature
 */
class SignLogic
{
    /**
     * 签名计算方法
     * @param string $date 请求时间 示例：Wed, 05 Sep. 2012 23:00:00 GMT   ，不强制要求时间格式，只是当做字符串处理
     * @param string $path 路径
     * @param string $query query参数
     * @param string $method 请求方法
     * @param string $body json主体
     * @param string $SecretKey 密钥
     * @param string $signature 请求签名
     * @return array
     */
    public static function signCalculate($date, $path, $query, $method, $body, $SecretKey, $signature)
    {
        $url = $path;
        if ($query) {
            $url .= '?' . $query;
        }
        $contentMd5 = '';
        if ($body) {
            $contentMd5 = strtolower(md5($body));
        }
        $sign = base64_encode(hash_hmac("sha1",
            $method . "\n"
            . $contentMd5 . "\n"
            . $date . "\n"
            . $url,
            $SecretKey));
        if ($sign != $signature) {
            $response = ['code' => 401, 'msg' => '签名不正确'];
            return $response;
        }

        $response = ['code' => 200, 'msg' => '鉴权成功'];
        return $response;
    }
}
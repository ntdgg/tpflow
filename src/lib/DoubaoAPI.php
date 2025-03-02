<?php

/**
 *+------------------
 * Tpflow AI豆包数据接口
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace tpflow\lib;

class DoubaoAPI
{
    private $apiUrl;
    private $apiKey;

    public function __construct($apiUrl, $apiKey)
    {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    // 调用豆包API的方法
    public function callModelAPI($model, $inputMessages)
    {
        try {
            // 准备POST数据
            $postData = [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => $inputMessages]]
            ];
            // 初始化cURL
            $ch = curl_init();
            // 设置cURL选项
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer {$this->apiKey}"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 执行cURL请求
            $response = curl_exec($ch);
            // 检查是否有错误
            if (curl_errno($ch)) {
                // 返回错误信息
                return [
                    'error' => curl_error($ch),
                    'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE)
                ];
            }
            // 关闭cURL会话
            curl_close($ch);
            $data = json_decode($response, true);
            // 返回响应数据
            return ['code'=>0,'msg'=>json_decode($data['choices'][0]['message']['content'])];
        } catch (\Exception $e) {
            // 返回错误信息
            return ['code'=>0,'msg'=>"ai:errot:{$e->getMessage()}"];
        }

    }
}


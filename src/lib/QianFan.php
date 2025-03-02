<?php

/**
 *+------------------
 * Tpflow 千帆AI接口
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace tpflow\lib;

class QianFan
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
        $postData = [
            'model' => $model,
            'input' => [
                'messages' => [['role' => 'user', 'content' => $inputMessages]]
            ],
            'parameters' => ['temperature' => '1.3', 'top_p' => '0.9','max_tokens'=>'2000']
        ];
        // 初始化 cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 执行 cURL 请求
        $response = curl_exec($ch);
        // 检查错误
        if (curl_errno($ch)) {
            return json([
                'error' => curl_error($ch)
            ], 500);
        }
        curl_close($ch);
        // 返回响应
            $data = json_decode($response, true);
            // 返回响应数据
            return ['code'=>0,'msg'=>json_decode($data['choices'][0]['message']['content'])];
        } catch (\Exception $e) {
            // 返回错误信息
            return ['code'=>0,'msg'=>"ai:errot:{$e->getMessage()}"];
        }
    }
}


<?php

namespace App\Traits;


trait SmsTraits
{
    /**
     * 发送短信
     * @param $mobile
     * @param $type
     * @return bool
     */
    public function sendSmsMsg($mobile, $type)
    {

        $smsId = '';
        switch ($type) {
            case 'register':
                $smsId = 'SMS_14270783';
                break;
            case 'forgetpwd':
                $smsId = 'SMS_14270781';
                break;
        }
        if (empty($smsId)) {
            return false;
        }

        $valid = random(4);
        $send = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => 'alibaba.aliqin.fc.sms.num.send',
            'app_key' => '23447163',
            'format' => 'JSON',
            'v' => '2.0',
            'sign_method' => 'md5',
            'sms_type' => 'normal',
            'sms_free_sign_name' => '毕昇网',
            'rec_num' => $mobile,
            'sms_template_code' => $smsId,
            'partner_id' => 'top-apitools',
            'sms_param' => '{"code":"' . $valid . '","product":"毕昇网"}'
        ];
        $send['sign_method'] = 'md5';
        $send['sign'] = $this->generateSign($send);

        $res = $this->curl("http://gw.api.taobao.com/router/rest", $send);
        $res = json_decode($res);
        if (isset($res->error_response)) {
            return false;
        }

        // 发送成功后存储验证码
        cache([$type . '_' . $mobile => $valid], 5);

        return true;
    }

    public function curl($url, $postFields = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "top-sdk-php");

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $reponse = curl_exec($ch);

        curl_close($ch);
        return $reponse;
    }

    /**
     * 生成加密sign
     * @param $params
     * @param string $secretKey
     * @return string
     */
    protected function generateSign($params, $secretKey = '3edb99d3a9cacca13efde825f4cd58ec')
    {
        ksort($params);

        $stringToBeSigned = $secretKey;
        foreach ($params as $k => $v) {
            if (is_string($v) && "@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $secretKey;

        return strtoupper(md5($stringToBeSigned));
    }

    protected function percentEncode($str)
    {
        // 使用urlencode编码后，将"+","*","%7E"做替换即满足 API规定的编码规范
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
}
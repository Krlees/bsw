<?php

if (!function_exists('human_filesize')) {
    /**
     * 返回更好的尺寸
     *
     * @param $bytes
     * @param int $decimals
     * @return string
     */
    function human_filesize($bytes, $decimals = 2)
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}

if (!function_exists('is_image')) {
    /**
     * 判断文件的MIME类型是否为图片
     */
    function is_image($mimeType)
    {
        return starts_with($mimeType, 'image/');
    }
}

if (!function_exists('array_add_field')) {
    /**
     * 在数组中插入指定的key和值 <递归>
     * @param $array
     * @param $filed
     * @param $value
     *
     * @return array
     */
    function array_add_field($array, $key, $value = true)
    {

        if (empty($array)) {
            return [];
        }

        foreach ($array as $k => $v) {
            $array[$k][$key] = $value;
            if (is_array($v)) {
                array_add_field($v, $key, $value);
            }
        }

        return $array;

    }
}

if (!function_exists('array2xml')) {
    /**
     * 数组转XML
     * @Author Krlee
     *
     * @param $arr
     * @return string
     */
    function array2xml($arr)
    {
        $xml = '<xml>';
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= '<' . $key . '>' . $val . '</' . $key . '>';
            } else {
                $xml .= '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
            }
        }
        $xml .= '</xml>';
        return $xml;
    }
}


if (!function_exists('custom_config')) {
    /**
     * 自定义错误码
     * @Author Krlee
     *
     * @param $code
     * @return mixed
     */
    function custom_config($code)
    {
        $arr = [
            '0' => '操作成功',
            '1004' => '缺少必须参数',
            '9000' => '数据库插入失败'
        ];

        return array_get($arr, $code, 80001);
    }
}

if (!function_exists('obj2arr')) {
    /**
     * 将pdo查询的结果对象转为数组array
     * @Author Krlee
     *
     * @param $obj
     * @return array
     */
    function obj2arr($obj)
    {
        return json_decode(json_encode($obj), true);
    }
}

if (!function_exists('curl_do')) {
    function curl_do($url, $header = '', $data = '', $method = false)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($header)) curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if ($method) curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}

if (!function_exists('picture')) {
    /**
     * 获取图片地址
     * @param <string> $name 图片名
     * @param <string> $module 所属模块
     * @param <string> $size 图片尺寸(默认中等)
     * @return <string>
     */
    function picture($name, $module, $size = 'medium')
    {

        if ($name) {
            $folder = 'm/';
            switch ($size) {
                case 'small':
                    $folder = 's/';
                    break;
                case 'large':
                    $folder = 'l/';
                    break;
                case 'source':
                    $folder = '';
                    break;
            }


            if (is_file(storage_path('uploads') . '/' . strtolower($module) . '/' . $folder . $name)) {
                return 'http://' . $_SERVER['HTTP_HOST'] . storage_path('uploads') . '/' . strtolower($module) . '/' . $folder . $name;
            }
        }
        return 'http://' . $_SERVER['HTTP_HOST'] . '/nopic.jpg';
    }
}

if (!function_exists('price_format')) {
    /**
     * 价格格式化
     *
     * @param int $price
     * @return string    $price_format
     */
    function price_format($price)
    {
        if ($price <= 0) {
            return '0.00';
        }

        return number_format($price, 2, '.', '');
    }
}

if (!function_exists('stripslashes_array')) {
    /**
     * 递归删除数组反斜杠
     * @param <array> $arr 数组
     */
    function stripslashes_array(&$arr)
    {
        foreach ($arr as $k => &$v) {
            $nk = stripslashes($k);
            if ($nk != $k) {
                $arr[$nk] = &$v;
                unset($arr[$k]);
            }
            if (is_array($v)) {
                stripslashes_array($v);
            } else {
                $arr[$nk] = stripslashes($v);
            }
        }
    }
}

if (!function_exists('multi_array_unique')) {
    /**
     * 删除二维数组中重复的值
     * @param <array> $multi_array 数组
     * @param <string> $unique_key 判断键值
     * @return <array>
     */
    function multi_array_unique($multi_array, $unique_key)
    {
        $unique_value = array();
        foreach ($multi_array as $k => $v) {
            if (in_array($v[$unique_key], $unique_value)) {
                unset($multi_array[$k]);
            } else {
                $unique_value[] = $v[$unique_key];
            }
        }
        return $multi_array;
    }
}

if (!function_exists('multi_array_sort')) {
    /**
     * 排序二维数组
     * @param <array> $multi_array 数组
     * @param <string> $sort_key 排序键值
     * @param <string> $sort 排列顺序
     * @return <array>
     */
    function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC)
    {
        if (is_array($multi_array)) {
            foreach ($multi_array as $row_array) {
                if (is_array($row_array)) {
                    $key_array[] = $row_array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
        array_multisort($key_array, $sort, $multi_array);
        return $multi_array;
    }
}

if (!function_exists('multi_array_values')) {
    /**
     * 提取键值
     * @param <array> $multi_array 数组
     * @param <string|array> $value_key 数据键名
     * @param <string> $id_key 编号键名（不赋值则使用默认键名）
     * @param <bool> $unique 清除重复项
     * @return <array|bool>
     */
    function multi_array_values($multi_array, $value_key, $id_key = '', $unique = false)
    {
        $array = array();
        $is_array = is_array($value_key);
        foreach ($multi_array as $v) {
            if ($is_array) {
                $data = array();
                foreach ($value_key as $k) {
                    $data[$k] = $v;
                }
            } else {
                $data = $v[$value_key];
            }
            if ($id_key) {
                $array[$v[$id_key]] = $data;
            } else {
                $array[] = $data;
            }
        }
        return $unique ? array_unique($array) : $array;
    }
}

if (!function_exists('is_mobile_request')) {
    /**
     * 判断手机访问
     */
    function is_mobile_request()
    {
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser = 0;
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom|IOS)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }
        if ((isset($_SERVER['HTTP_ACCEPT'])) && (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== FALSE)) {
            $mobile_browser++;
        }
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            $mobile_browser++;
        }
        if (isset($_SERVER['HTTP_PROFILE'])) {
            $mobile_browser++;
        }
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
            'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
            'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
            'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
            'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
            'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
            'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
            'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
            'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
        );
        if (in_array($mobile_ua, $mobile_agents)) {
            $mobile_browser++;
        }
        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== FALSE) {
            $mobile_browser++;
        }
        // Pre-final check to reset everything if the user is on Windows
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== FALSE) {
            $mobile_browser = 0;
        }
        // But WP7 is also Windows, with a slightly different characteristic
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== FALSE) {
            $mobile_browser++;
        }
        if ($mobile_browser > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}





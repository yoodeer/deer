<?php


namespace YooDeer\Utils;


class Deer
{

    /**
     * @param string $mobile
     * @return false|int
     */
    public static function isMobile($mobile)
    {
        $pattern = '/^1[3456789]{1}\d{9}$/';

        return preg_match($pattern, $mobile, $matches);
    }

    /**
     * @param $mobile
     * @return mixed
     */
    public static function replaceMobile($mobile)
    {
        return substr_replace($mobile, '****', 3, 4);
    }

    /**
     * @param $idCard
     * @return bool
     */
    public static function isIdCard($idCard)
    {
        if (strlen($idCard) != 18) return false;

        $idCardBase = substr($idCard, 0, 17);

        $verify_code = substr($idCard, 17, 1);

        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

        $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

        $total = 0;

        for ($i = 0; $i < 17; $i++) {
            $total += substr($idCardBase, $i, 1) * $factor[$i];
        }

        $mod = $total % 11;

        return $verify_code == $verify_code_list[$mod];
    }

    /**
     * 提取富文本字符串的纯文本
     *
     * @param $string
     * @param int $num
     * @return string
     */
    public static function richTextExtractText($string, $num = 0)
    {
        $html_string = htmlspecialchars_decode($string);

        $content = str_replace(" ", "", $html_string);

        $contents = strip_tags($content);

        return mb_strlen($contents, 'utf-8') > $num ? mb_substr($contents, 0, $num, "utf-8") . '....' : mb_substr($contents, 0, $num, "utf-8");
    }

    /**
     * @param $second
     * @return string
     */
    public static function timeToDateString($second)
    {
        $day = floor($second / (3600 * 24));

        $second = $second % (3600 * 24);

        $hour = floor($second / 3600);

        $second = $second % 3600;

        $minute = floor($second / 60);

        $second = $second % 60;

        return $day . '天' . $hour . '小时' . $minute . '分' . $second . '秒';
    }


    /**
     * 下划线转驼峰
     *
     * @param array $data
     * @return array
     */
    public static function convertHump(array $data)
    {
        $result = [];

        foreach ($data as $key => $item) {
            if (is_array($item) || is_object($item)) {
                $result[self::convertUnderline($key)] = self::convertHump((array)$item);
            } else {
                $result[self::convertUnderline($key)] = $item;
            }
        }

        return $result;
    }

    /**
     * 驼峰转下划线
     *
     * @param array $data
     * @return array
     */
    public static function convertLine(array $data)
    {
        $result = [];

        foreach ($data as $key => $item) {
            if (is_array($item) || is_object($item)) {
                $result[self::humpToLine($key)] = self::convertLine((array)$item);
            } else {
                $result[self::humpToLine($key)] = $item;
            }
        }

        return $result;
    }

    /**
     * @param $str
     * @return string|string[]|null
     */
    public static function convertUnderline($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
            return strtoupper($matches[2]);
        }, $str);

        return $str;
    }

    /**
     * @param $str
     * @return string|string[]|null
     */
    public static function humpToLine($str)
    {
        $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
            return '_' . strtolower($matches[0]);
        }, $str);

        return $str;
    }

    /**
     * 返回当前时差
     *
     * @param $date
     * @param int $type
     * @return string
     */
    public static function formatTime($date, $type = 1)
    {
        date_default_timezone_set('PRC'); //中国的时区

        switch ($type) {
            case 1:
                $second = time() - $date;
                $minute = floor($second / 60) ? floor($second / 60) : 1;
                if ($minute >= 60 && $minute < (60 * 24)) {
                    $hour = floor($minute / 60);
                } elseif ($minute >= (60 * 24) && $minute < (60 * 24 * 30)) {
                    $day = floor($minute / (60 * 24));
                } elseif ($minute >= (60 * 24 * 30) && $minute < (60 * 24 * 365)) {
                    $month = floor($minute / (60 * 24 * 30));
                } elseif ($minute >= (60 * 24 * 365)) {
                    $year = floor($minute / (60 * 24 * 365));
                }

                break;

            case 2:
                $date = strtotime($date);
                $second = time() - $date;
                $minute = floor($second / 60) ? floor($second / 60) : 1;
                if ($minute >= 60 && $minute < (60 * 24)) {
                    $hour = floor($minute / 60);
                } elseif ($minute >= (60 * 24) && $minute < (60 * 24 * 30)) {
                    $day = floor($minute / (60 * 24));
                } elseif ($minute >= (60 * 24 * 30) && $minute < (60 * 24 * 365)) {
                    $month = floor($minute / (60 * 24 * 30));
                } elseif ($minute >= (60 * 24 * 365)) {
                    $year = floor($minute / (60 * 24 * 365));
                }

                break;

            default:

                break;
        }

        if (isset($year)) {
            return $year . '年前';
        } elseif (isset($month)) {
            return $month . '月前';
        } elseif (isset($day)) {
            return $day . '天前';
        } elseif (isset($hour)) {
            return $hour . '小时前';
        } elseif (isset($minute)) {
            return $minute . '分钟前';
        }
    }

    /**
     * 随机字符串
     *
     * @param $length
     * @param $type
     * @param bool $convert
     * @return string
     */
    public static function random($length, $type, $convert = false)
    {
        $config = [
            'number' => '1234567890',
            'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
            'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ];

        if (!isset($config[$type])) $type = 'string';

        $string = $config[$type];

        $code = '';

        $strLen = strlen($string) - 1;

        for ($i = 0; $i < $length; $i++) {
            $code .= $string{mt_rand(0, $strLen)};
        }

        if ($convert) $code = $convert ? strtoupper($code) : strtolower($code);

        return $code;
    }
}
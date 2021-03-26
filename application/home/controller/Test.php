<?php

/*
 * 脚本
 */
namespace app\home\controller;
use think\Lang;
use think\Controller;

use think\Db;
class Test extends Controller
{


    public function syn(){


        ini_set('memory_limit','3072M');    // 临时设置最大内存占用为3G
        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期

        $res = Db::name('ip')->where('country' ,' ')->limit(30)->select();

        echo "获取了". count($res).'个'."\n";

        foreach($res as $key => $val){
            $country = '';
            $city_code = '';
            $address = '';
            $point_x = '';
            $point_y = '';
            $ip_info = '';
            $ipAddress = $this->get_ip_address($val['ip']);

            if($ipAddress['status'] == 0){

                $addressArray = explode('|',$ipAddress['address'] );

                $country = $this->transCode($addressArray[0]);

                //具体地址
                $address = $ipAddress['content']['address'];
                $city_code = $ipAddress['content']['address_detail']['city_code'];
                $point_x = $ipAddress['content']['point']['x'];
                $point_y = $ipAddress['content']['point']['y'];
                $ip_info = json_encode($ipAddress);

            }else{

                $array = $this->getAddressgetByIPAPI($val['ip']);
                $country = $this->transCode($array['countryCode']);

                $address = $array['regionName'].'/'.$array['city'];
                $city_code = $array['countryCode'] ?$array['countryCode']: '' ;
                $point_x = $array['lat'];
                $point_y = $array['lon'];
                $ip_info = json_encode($array);

            }


            $data =[
                'country' => $country ?$country: '未知IP' ,
                'address' => $address ? $address : '',
                'city_code' => $city_code ? $city_code : '',
                'point_x' => $point_x ? $point_x :'',
                'point_y' => $point_y ? $point_y : '',
                'ip_info' => $ip_info ? $ip_info :'',
            ];

            $res =  Db::name('ip')->where('id', $val['id'])->update($data);
            if($res){
                echo "完成". $val['id']."\n";
            }
        }
        echo "修改完成". count($res).'个'."\n";die;

    }

//查不到 在查这个
    protected function getAddressgetByIPAPI($ip){

        $url = "http://ip-api.com/json/$ip";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'CURL ERROR Code: ' . curl_errno($ch) . ', reason: ' . curl_error($ch);
        }
        curl_close($ch);
        $info = json_decode($output, true);
        return $info;
    }



    protected  function get_ip_address($ip)
    {

        // 获取当前位置所在城市
        $content = file_get_contents("http://api.map.baidu.com/location/ip?ak=2TGbi6zzFm5rjYKqPPomh9GBwcgLW5sS&ip={$ip}&coor=bd09ll");
        $json =  json_decode( $content,true);
        return $json;

    }

    //国家
    protected function transCode($code)
    {
        $ind = array('AA' => '阿鲁巴',
            'AD' => '安道尔',
            'AE' => '阿联酋',
            'AF' => '阿富汗',
            'AG' => '安提瓜和巴布达',
            'AL' => '阿尔巴尼亚',
            'AM' => '亚美尼亚',
            'AN' => '荷属安德列斯',
            'AO' => '安哥拉',
            'AQ' => '南极洲',
            'AR' => '阿根廷',
            'AS' => '东萨摩亚',
            'AT' => '奥地利',
            'AU' => '澳大利亚',
            'AZ' => '阿塞拜疆',
            'Av' => '安圭拉岛',
            'BA' => '波黑',
            'BB' => '巴巴多斯',
            'BD' => '孟加拉',
            'BE' => '比利时',
            'BF' => '巴哈马',
            'BF' => '布基纳法索',
            'BG' => '保加利亚',
            'BH' => '巴林',
            'BI' => '布隆迪',
            'BJ' => '贝宁',
            'BM' => '百慕大',
            'BN' => '文莱布鲁萨兰',
            'BO' => '玻利维亚',
            'BR' => '巴西',
            'BS' => '巴哈马',
            'BT' => '不丹',
            'BV' => '布韦岛',
            'BW' => '博茨瓦纳',
            'BY' => '白俄罗斯',
            'BZ' => '伯里兹',
            'CA' => '加拿大',
            'CB' => '柬埔寨',
            'CC' => '可可斯群岛',
            'CD' => '刚果',
            'CF' => '中非',
            'CG' => '刚果',
            'CH' => '瑞士',
            'CI' => '象牙海岸',
            'CK' => '库克群岛',
            'CL' => '智利',
            'CM' => '喀麦隆',
            'CN' => '中国',
            'CO' => '哥伦比亚',
            'CR' => '哥斯达黎加',
            'CS' => '捷克斯洛伐克',
            'CU' => '古巴',
            'CV' => '佛得角',
            'CX' => '圣诞岛',
            'CY' => '塞普路斯',
            'CZ' => '捷克',
            'DE' => '德国',
            'DJ' => '吉布提',
            'DK' => '丹麦',
            'DM' => '多米尼加共和国',
            'DO' => '多米尼加联邦',
            'DZ' => '阿尔及利亚',
            'EC' => '厄瓜多尔',
            'EE' => '爱沙尼亚',
            'EG' => '埃及',
            'EH' => '西撒哈拉',
            'ER' => '厄立特里亚',
            'ES' => '西班牙',
            'ET' => '埃塞俄比亚',
            'FI' => '芬兰',
            'FJ' => '斐济',
            'FK' => '福兰克群岛',
            'FM' => '米克罗尼西亚',
            'FO' => '法罗群岛',
            'FR' => '法国',
            'FX' => '法国-主教区',
            'GA' => '加蓬',
            'GB' => '英国',
            'GD' => '格林纳达',
            'GE' => '格鲁吉亚',
            'GF' => '法属圭亚那',
            'GH' => '加纳',
            'GI' => '直布罗陀',
            'GL' => '格陵兰岛',
            'GM' => '冈比亚',
            'GN' => '几内亚',
            'GP' => '法属德洛普群岛',
            'GQ' => '赤道几内亚',
            'GR' => '希腊',
            'GS' => 'S. Georgia and S. Sandwich Isls.',
            'GT' => '危地马拉',
            'GU' => '关岛',
            'GW' => '几内亚比绍',
            'GY' => '圭亚那',
            'HK' => '中国香港特区',
            'HM' => '赫德和麦克唐纳群岛',
            'HN' => '洪都拉斯',
            'HR' => '克罗地亚',
            'HT' => '海地',
            'HU' => '匈牙利',
            'ID' => '印度尼西亚',
            'IE' => '爱尔兰',
            'IL' => '以色列',
            'IN' => '印度',
            'IO' => '英属印度洋领地',
            'IQ' => '伊拉克',
            'IR' => '伊朗',
            'IS' => '冰岛',
            'IT' => '意大利',
            'JM' => '牙买加',
            'JO' => '约旦',
            'JP' => '日本',
            'KE' => '肯尼亚',
            'KG' => '吉尔吉斯斯坦',
            'KH' => '柬埔寨',
            'KI' => '基里巴斯',
            'KM' => '科摩罗',
            'KN' => '圣基茨和尼维斯',
            'KP' => '韩国',
            'KR' => '朝鲜',
            'KW' => '科威特',
            'KY' => '开曼群岛',
            'KZ' => '哈萨克斯坦',
            'LA' => '老挝',
            'LB' => '黎巴嫩',
            'LC' => '圣卢西亚',
            'LI' => '列支顿士登',
            'LK' => '斯里兰卡',
            'LR' => '利比里亚',
            'LS' => '莱索托',
            'LT' => '立陶宛',
            'LU' => '卢森堡',
            'LV' => '拉托维亚',
            'LY' => '利比亚',
            'MA' => '摩洛哥',
            'MC' => '摩纳哥',
            'MD' => '摩尔多瓦',
            'MG' => '马达加斯加',
            'MH' => '马绍尔群岛',
            'MK' => '马其顿',
            'ML' => '马里',
            'MM' => '缅甸',
            'MN' => '蒙古',
            'MO' => '中国澳门特区',
            'MP' => '北马里亚纳群岛',
            'MQ' => '法属马提尼克群岛',
            'MR' => '毛里塔尼亚',
            'MS' => '蒙塞拉特岛',
            'MT' => '马耳他',
            'MU' => '毛里求斯',
            'MV' => '马尔代夫',
            'MW' => '马拉维',
            'MX' => '墨西哥',
            'MY' => '马来西亚',
            'MZ' => '莫桑比克',
            'NA' => '纳米比亚',
            'NC' => '新卡里多尼亚',
            'NE' => '尼日尔',
            'NF' => '诺福克岛',
            'NG' => '尼日利亚',
            'NI' => '尼加拉瓜',
            'NL' => '荷兰',
            'NO' => '挪威',
            'NP' => '尼泊尔',
            'NR' => '瑙鲁',
            'NT' => '中立区(沙特-伊拉克间)',
            'NU' => '纽爱',
            'NZ' => '新西兰',
            'OM' => '阿曼',
            'PA' => '巴拿马',
            'PE' => '秘鲁',
            'PF' => '法属玻里尼西亚',
            'PG' => '巴布亚新几内亚',
            'PH' => '菲律宾',
            'PK' => '巴基斯坦',
            'PL' => '波兰',
            'PM' => '圣皮艾尔和密克隆群岛',
            'PN' => '皮特克恩岛',
            'PR' => '波多黎各',
            'PT' => '葡萄牙',
            'PW' => '帕劳',
            'PY' => '巴拉圭',
            'QA' => '卡塔尔',
            'RE' => '法属尼留旺岛',
            'RO' => '罗马尼亚',
            'RU' => '俄罗斯',
            'RW' => '卢旺达',
            'SA' => '沙特阿拉伯',
            'SC' => '塞舌尔',
            'SD' => '苏丹',
            'SE' => '瑞典',
            'SG' => '新加坡',
            'SH' => '圣赫勒拿',
            'SI' => '斯罗文尼亚',
            'SJ' => '斯瓦尔巴特和扬马延岛',
            'SK' => '斯洛伐克',
            'SL' => '塞拉利昂',
            'SM' => '圣马力诺',
            'SN' => '塞内加尔',
            'SO' => '索马里',
            'SR' => '苏里南',
            'ST' => '圣多美和普林西比',
            'SU' => '前苏联',
            'SV' => '萨尔瓦多',
            'SY' => '叙利亚',
            'SZ' => '斯威士兰',
            'Sb' => '所罗门群岛',
            'TC' => '特克斯和凯科斯群岛',
            'TD' => '乍得',
            'TF' => '法国南部领地',
            'TG' => '多哥',
            'TH' => '泰国',
            'TJ' => '塔吉克斯坦',
            'TK' => '托克劳群岛',
            'TM' => '土库曼斯坦',
            'TN' => '突尼斯',
            'TO' => '汤加',
            'TP' => '东帝汶',
            'TR' => '土尔其',
            'TT' => '特立尼达和多巴哥',
            'TV' => '图瓦卢',
            'TW' => '中国台湾省',
            'TZ' => '坦桑尼亚',
            'UA' => '乌克兰',
            'UG' => '乌干达',
            'UK' => '英国',
            'UM' => '美国海外领地',
            'US' => '美国',
            'UY' => '乌拉圭',
            'UZ' => '乌兹别克斯坦',
            'VA' => '梵蒂岗',
            'VC' => '圣文森特和格陵纳丁斯',
            'VE' => '委内瑞拉',
            'VG' => '英属维京群岛',
            'VI' => '美属维京群岛',
            'VN' => '越南',
            'VU' => '瓦努阿鲁',
            'WF' => '瓦里斯和福图纳群岛',
            'WS' => '西萨摩亚',
            'YE' => '也门',
            'YT' => '马约特岛',
            'YU' => '南斯拉夫',
            'ZA' => '南非',
            'ZM' => '赞比亚',
            'ZR' => '扎伊尔',
            'ZW' => '津巴布韦');
        $code = strtoupper($code);
        $name = isset($ind[$code]) ? $ind[$code] : '局域网';
        if (empty($name)) {
            return null;
        }
        return $name;
    }

}
<?php

/*
 * 首页相关基本调用
 */
namespace app\home\controller;
use think\Lang;
use think\Db;
class Index extends BaseMall
{
    const rows = 15;
    const page = 1;

    // 音乐默认值
    const for_one = 1;  //列表循环播放 默认
    const for_two = 2;  //单曲播放
    const for_three = 3;  //随机播放

    const syns_no = 1;  //关闭音乐同步 默认
    const syns_yes = 2;  //开启音乐同步

    public function _initialize()
    {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/index.lang.php');
    }


    protected  function get_ip_address($ip)
    {

        // 获取当前位置所在城市
        $content = file_get_contents("http://api.map.baidu.com/location/ip?ak=2TGbi6zzFm5rjYKqPPomh9GBwcgLW5sS&ip={$ip}&coor=bd09ll");
        $json =  json_decode( $content,true);
        return $json;

    }


    //记录IP
    protected  function get_client_ip($type) {

           $ip = $_SERVER['REMOTE_ADDR'];
           if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
               $ip = $_SERVER['HTTP_CLIENT_IP'];
           } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
               foreach ($matches[0] AS $xip) {
                   if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                       $ip = $xip;
                       break;
                   }
               }
           }
           $ipAddress = $this->get_ip_address($ip);
           $country = '';
           $city_code = '';
           $address = '';
           $point_x = '';
           $point_y = '';
           if($ipAddress['status'] == 0){

               $addressArray = explode('|',$ipAddress['address'] );

               $country = $this->transCode($addressArray[0]);

               //具体地址
               $address = $ipAddress['content']['address'];
               $city_code = $ipAddress['content']['address_detail']['city_code'];
               $point_x = $ipAddress['content']['point']['x'];
               $point_y = $ipAddress['content']['point']['y'];

           }

            if($type == 'pc'){
                    $data =[
                        'ip' => $ip,
                        'type' => $type,
                        'info' => '浏览器信息：'.$this->GetBrowser(),
                        'country' => $country,
                        'address' => $address,
                        'city_code' => $city_code,
                        'point_x' => $point_x,
                        'point_y' => $point_y,
                        'ip_info' => json_encode($ipAddress),
                        'ceateTime' => time(),
                    ];
                    model('music')->addIp($data);

            }else if($type == 'web'){

                $data =[
                    'ip' => $ip,
                    'type' => $type,
                    'info' => '设备：'.$this->mobile_type() . '版本'.$this->getOS() ,
                    'country' => $country,
                    'address' => $address,
                    'city_code' => $city_code,
                    'point_x' => $point_x,
                    'point_y' => $point_y,
                    'ip_info' => json_encode($ipAddress),
                    'ceateTime' => time()
                ];
                model('music')->addIp($data);

            }

        }

    protected function GetBrowser(){
        if(!empty($_SERVER['HTTP_USER_AGENT'])){
            $br = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/MSIE/i',$br)) {
                $br = 'MSIE';
            }elseif (preg_match('/Firefox/i',$br)) {
                $br = 'Firefox';
            }elseif (preg_match('/Chrome/i',$br)) {
                $br = 'Chrome';
            }elseif (preg_match('/Safari/i',$br)) {
                $br = 'Safari';
            }elseif (preg_match('/Opera/i',$br)) {
                $br = 'Opera';
            }else {
                $br = 'Other';
            }
            return $br;
        }else{
            return "获取浏览器信息失败！";
        }
    }
//     版本
    protected function getOS()
    {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($ua, 'Android') !== false) {
            preg_match("/(?<=Android )[\d\.]{1,}/", $ua, $version);
            return 'Platform:Android OS_Version:'.$version[0];
        } elseif (strpos($ua, 'iPhone') !== false) {
            preg_match("/(?<=CPU iPhone OS )[\d\_]{1,}/", $ua, $version);
            return 'Platform:iPhone OS_Version:'.str_replace('_', '.', $version[0]);
        } elseif (strpos($ua, 'iPad') !== false) {
            preg_match("/(?<=CPU OS )[\d\_]{1,}/", $ua, $version);
            return 'Platform:iPad OS_Version:'.str_replace('_', '.', $version[0]);
        }

    }

    //手机型号
    protected function mobile_type()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (stripos($user_agent, "iPhone")!==false) {
            $brand = 'iPhone';
        } else if (stripos($user_agent, "SAMSUNG")!==false || stripos($user_agent, "Galaxy")!==false || strpos($user_agent, "GT-")!==false || strpos($user_agent, "SCH-")!==false || strpos($user_agent, "SM-")!==false) {
            $brand = '三星';
        } else if (stripos($user_agent, "Huawei")!==false || stripos($user_agent, "Honor")!==false || stripos($user_agent, "H60-")!==false || stripos($user_agent, "H30-")!==false) {
            $brand = '华为';
        } else if (stripos($user_agent, "Lenovo")!==false) {
            $brand = '联想';
        } else if (strpos($user_agent, "MI-ONE")!==false || strpos($user_agent, "MI 1S")!==false || strpos($user_agent, "MI 2")!==false || strpos($user_agent, "MI 3")!==false || strpos($user_agent, "MI 4")!==false || strpos($user_agent, "MI-4")!==false) {
            $brand = '小米';
        } else if (strpos($user_agent, "HM NOTE")!==false || strpos($user_agent, "HM201")!==false) {
            $brand = '红米';
        } else if (stripos($user_agent, "Coolpad")!==false || strpos($user_agent, "8190Q")!==false || strpos($user_agent, "5910")!==false) {
            $brand = '酷派';
        } else if (stripos($user_agent, "ZTE")!==false || stripos($user_agent, "X9180")!==false || stripos($user_agent, "N9180")!==false || stripos($user_agent, "U9180")!==false) {
            $brand = '中兴';
        } else if (stripos($user_agent, "OPPO")!==false || strpos($user_agent, "X9007")!==false || strpos($user_agent, "X907")!==false || strpos($user_agent, "X909")!==false || strpos($user_agent, "R831S")!==false || strpos($user_agent, "R827T")!==false || strpos($user_agent, "R821T")!==false || strpos($user_agent, "R811")!==false || strpos($user_agent, "R2017")!==false) {
            $brand = 'OPPO';
        } else if (strpos($user_agent, "HTC")!==false || stripos($user_agent, "Desire")!==false) {
            $brand = 'HTC';
        } else if (stripos($user_agent, "vivo")!==false) {
            $brand = 'vivo';
        } else if (stripos($user_agent, "K-Touch")!==false) {
            $brand = '天语';
        } else if (stripos($user_agent, "Nubia")!==false || stripos($user_agent, "NX50")!==false || stripos($user_agent, "NX40")!==false) {
            $brand = '努比亚';
        } else if (strpos($user_agent, "M045")!==false || strpos($user_agent, "M032")!==false || strpos($user_agent, "M355")!==false) {
            $brand = '魅族';
        } else if (stripos($user_agent, "DOOV")!==false) {
            $brand = '朵唯';
        } else if (stripos($user_agent, "GFIVE")!==false) {
            $brand = '基伍';
        } else if (stripos($user_agent, "Gionee")!==false || strpos($user_agent, "GN")!==false) {
            $brand = '金立';
        } else if (stripos($user_agent, "HS-U")!==false || stripos($user_agent, "HS-E")!==false) {
            $brand = '海信';
        } else if (stripos($user_agent, "Nokia")!==false) {
            $brand = '诺基亚';
        } else {
            $brand = '其他手机';
        }
        return $brand;
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


    public function  syn(){

//        ini_set('memory_limit','3072M');    // 临时设置最大内存占用为3G
        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期

        $res = Db::name('ip')->where('id','>',1690)->select();

        foreach($res as $key => $val){

            $ipAddress = $this->get_ip_address($val['ip']);
            $country = '';
            $city_code = '';
            $address = '';
            $point_x = '';
            $point_y = '';

            if($ipAddress['status'] == 0){

                $addressArray = explode('|',$ipAddress['address'] );

                $country = $this->transCode($addressArray[0]);

                //具体地址
                $address = $ipAddress['content']['address'];
                $city_code = $ipAddress['content']['address_detail']['city_code'];
                $point_x = $ipAddress['content']['point']['x'];
                $point_y = $ipAddress['content']['point']['y'];

            }

            $data =[
                'country' => $country,
                'address' => $address,
                'city_code' => $city_code,
                'point_x' => $point_x,
                'point_y' => $point_y,
                'ip_info' => json_encode($ipAddress),
            ];


           $res =  Db::name('ip')->where('id', $val['id'])->update($data);
           if($res){
               echo "完成". $val['id']."\n";
           }

        }
        die;
    }

    /**
     *  音乐列表
     *
     *
     * @method getMode
     */

    public function index(){


        $curpage = input('page') ? input('page') : self::page;//当前第x页

        $data = model('music')->set();

        if($data['syns'] == self::syns_yes){

            $this->syns();

        }

        if($this->isMobile()){

            $musicShou =  model('music')->musicShou(20);

        }else{

            $musicShou =  model('music')->musicShou();
        }



        $cmf_arr = array_column($musicShou, 'num');
        array_multisort($cmf_arr, SORT_DESC, $musicShou);

        $seach = trim($this->request->get('seach'));
        $title = trim($this->request->get('title'));

        if(!empty($title) && !empty($seach)){

            $list = $this->music_show_yes($data,['artist' => $seach,'title' => $title]);


        }else if(!empty($title)){

            $list = $this->music_show_no($data,['title' => $title]);


        }elseif(!empty($seach)){

            $list = $this->music_show_yes($data,['artist' => $seach]);

        }else{
            $list =  $this->music_show_no($data);

        }



        $this->assign('fors', $data['fores']);
        $this->assign('syns', $data['syns']);

        $this->assign('title', $title);

        $this->assign('seach', $seach);

        $this->assign('curpage', $curpage);

        $this->assign('musicShou', $musicShou);

        $this->assign('json',  json_encode(array_values($list),JSON_UNESCAPED_SLASHES));//json 格式化

        if($this->isMobile()){
            $this->get_client_ip('web');

            //跳转移动端页面
            return $this->fetch($this->template_dir . 'mobile');

        }else{

            $this->get_client_ip('pc');

            //跳转PC端页面
            return $this->fetch($this->template_dir . 'ind');

        }
    }

    //分页

    public function page(){

        $curpage = input('page') ? input('page') : self::page;//当前第x页

        $title = input('title') ? input('title') : '';

        if($title){
            $condition = [
                'title' => $title
            ];
        }else{
            $condition = '';
        }

        if($this->isMobile()){

            $music =  model('music')->musicPage($condition, $curpage,10);

        }else{

            $music =  model('music')->musicPage($condition , $curpage);

        }


        if (!empty($music)) {
            $arr = array('status' => 200, 'Code' => "1", 'info' => $music['data'],'page' => $music['page']);
        }else{
            $arr = array('status' => 1001, 'Code' => "1", 'info' => "暂无数据");
        }
        return json($arr);
    }

    //$seach用
    protected function music_show_yes($data,$where){

        $musicAll =  model('music')->musicAll();

        $musicFind =  model('music')->musicFind($where);

        $list = [];

        switch ($data['fores']){

            case self::for_one:

                foreach($musicAll as $key => $val){

                    if($val['id'] <= $musicFind['id']){
                        $list[$key]['title'] = $val['title'];
                        $list[$key]['artist'] = $val['artist'];
                        $list[$key]['mp3'] = $val['mp3'];
                    }
                }


                break;
            case self::for_two:

                for ($i=1; $i<=count($musicAll); $i++)
                {
                    $list[$i]['title'] = $musicFind['title'];
                    $list[$i]['artist'] = $musicFind['artist'];
                    $list[$i]['mp3'] = $musicFind['mp3'];
                }


                break;

            case self::for_three:

                shuffle($musicAll);
                foreach($musicAll as $key => $val){

                    $list[$key]['title'] = $val['title'];
                    $list[$key]['artist'] = $val['artist'];
                    $list[$key]['mp3'] = $val['mp3'];
                }

                array_unshift($list,$musicFind);
                break;

        }

        return $list;

    }

    //空用
    protected function music_show_no($data,$where = ''){
        $musicAll =  model('music')->musicAll($where);


        $list = [];

        switch ($data['fores']){

            case self::for_one:

                foreach($musicAll as $key => $val){

                    $list[$key]['title'] = $val['title'];
                    $list[$key]['artist'] = $val['artist'];
                    $list[$key]['mp3'] = $val['mp3'];

                }
                break;

            case self::for_two:



                for ($i=1; $i<=count($musicAll); $i++)
                {
                    $list[$i]['title'] = $musicAll[0]['title'];
                    $list[$i]['artist'] = $musicAll[0]['artist'];
                    $list[$i]['mp3'] = $musicAll[0]['mp3'];
                }
                break;

            case self::for_three:

                shuffle($musicAll);

                foreach($musicAll as $key => $val){

                    $list[$key]['title'] = $val['title'];
                    $list[$key]['artist'] = $val['artist'];
                    $list[$key]['mp3'] = $val['mp3'];

                }
                break;

        }

        return $list;

    }


    //修改播放模式和同步开关
    public function status(){

        $type = $this->request->post('type');


        if($type == 1){

            $fores = $this->request->post('fores');

            $music =  model('music')->edit(1,['fores' => $fores]);


        }else{

            $syns = $this->request->post('syns');

            $music =  model('music')->edit(1,['syns' => $syns]);

        }

        if($music){

            return json("200");
        }

    }


    //从文件夹中同步音乐到 数据库
    protected function syns(){


        $file_path="uploads/music";

        $data = $this->folder_list($file_path);//遍历当前目录

        foreach($data as $key => $val){

            model('music')->music_Insert($val);


        }

    }


    //遍历出目录
    protected function folder_list($dir){
        $dir .= substr($dir, -1) == '/' ? '' : '/';
        $dirInfo = array();
        foreach (glob($dir.'*') as $v) {


            $vs = explode("/",$v);


            $music = explode("-",$vs[2]);

            $artist = str_replace(strrchr($music[1], "."),"",$music[1]);

            $dirInfo[] = ['title' => $music[0],'artist' =>$artist ,'mp3' => 'http://'.$_SERVER['SERVER_NAME'] .'/'.$v ,'poster' =>'' ];
            if(is_dir($v)){
                $dirInfo = array_merge($dirInfo, $this->folder_list($v));
            }
        }

        return $dirInfo;
    }




    protected function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
        {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }

}
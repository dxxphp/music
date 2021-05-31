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

            if($type == 'pc'){
                    $data =[
                        'ip' => $ip,
                        'type' => $type,
                        'info' => '浏览器信息：'.$this->GetBrowser(),
                        'ceateTime' => time(),
                        'url' => urldecode($_SERVER [ "REQUEST_URI" ]),
                    ];
                    model('music')->addIp($data);

            }else if($type == 'web'){

                $data =[
                    'ip' => $ip,
                    'type' => $type,
                    'info' => '设备：'.$this->mobile_type() . '版本'.$this->getOS() ,
                    'ceateTime' => time(),
                    'url' => urldecode($_SERVER [ "REQUEST_URI" ]),
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


///usr/bin/php index.php home/test/syn
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

    //点击无感切换歌曲
    public function cut(){
        $id = $this->request->post('id');

        $musicData =  model('music')->musicIdData($id);

        return json($musicData);
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
<?php

/*
 * 首页相关基本调用
 */
namespace app\home\controller;
use think\Lang;
use think\Db;
class Test
{
    const rows = 15;
    const page = 1;

    // 音乐默认值
    const for_one = 1;  //列表循环播放 默认
    const for_two = 2;  //单曲播放
    const for_three = 3;  //随机播放

    const syns_no = 1;  //关闭音乐同步 默认
    const syns_yes = 2;  //开启音乐同步



    public function syn(){

        echo "lao B";die;
    }




}
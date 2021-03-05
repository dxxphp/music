<?php

namespace app\common\model;

use think\Model;

class Music extends Model
{
    public $page_info;


    /**
     * 查询ip
     *
     * @access set_ip
     * @author duxinxin
     * @date 2020/04/26
     */
    public function set_ip($ip,$type){

        return db('ip')->field('*')->where(['ip' => $ip,'type' =>$type])->find();

    }

    /**
     * 查询ip
     *
     * @access set_ip
     * @author duxinxin
     * @date 2020/04/26
     */
    public function addIp($data){

        return   db('ip')->insert($data);

    }

    /**
     * 修改 ip +1
     *
     * @access set_ip
     * @author duxinxin
     * @date 2020/04/26
     */
    public function updateIp($ip,$type){

        return   db('ip')->where(['ip' => $ip])->setInc('num');

    }

    /**
     * 音乐列表添加操作
     *
     * @access music_Insert
     * @author duxinxin
     * @date 2020/04/26
     */
    public function music_Insert($data){

        return   db('music')->insert($data,true);

    }

    /**
     * 音乐列表歌手分组
     *
     * @access musicShou
     * @author duxinxin
     * @date 2020/04/26
     */
    public function musicShou($num = 40){

        return db('music')
            ->field('title,count(id) as num')
            ->group('title')
            ->limit($num)
            ->select();

    }

    /**
     * 用户修改数据操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit($id, $condition){

        return    db('set')->where('id', $id)->update($condition);


    }

    /**
     * 查询设置
     *
     * @access music_Insert
     * @author duxinxin
     * @date 2020/04/26
     */
    public function set($field = '*'){

        return db('set')->field($field)->where(['id' => 1])->find();

    }

    /**
     * 音乐查询一条
     *
     * @access musicFind
     * @author duxinxin
     * @date 2020/04/26
     */
    public function musicFind($condition){

        return  db('music')->field(['id','title','artist','mp3','poster'])->where($condition)->find();

    }

    /**
     * 查询音乐列表集合
     *
     * @access classPage
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  musicPage($condition = '' , $curpage, $page = 20){
        //查询集合数量

        $totalItem = db('music')
//            ->whereLike('classname',"%".$condition['classname']."%")
            ->where($condition)
            ->count('id');

        //总页数
        $totalPage = ceil($totalItem/$page);
        $startItem = ($curpage-1) * $page;

        //查询集合并分页
        $news = db('music')
            ->field(['id','title','artist','mp3','poster'])
//            ->whereLike('classname',"%".$condition['classname']."%")
            ->where($condition)
            ->order('id DESC')
            ->limit($startItem,$page)
            ->select();

        $pages['totalItem'] = $totalItem;
        $pages['pageSize'] = $page;
        $pages['totalPage'] = $totalPage;

        $show = [
            'page'=>$pages,
            'data' =>$news,
        ];

        return $show;
    }

    /**
     * 查询音乐全部集合
     *
     * @access classPage
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  musicAll($condition = ''){

        //查询集合
        $arr =  db('music')
            ->field(['id','title','artist','mp3','poster'])
            ->where($condition)
            ->order('id DESC')
            ->limit(200)
            ->select();

        return $arr;
    }
}

?>
